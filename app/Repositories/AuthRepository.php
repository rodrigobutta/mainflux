<?php
namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use App\Events\UserLogin;
use Illuminate\Support\Str;
use App\Notifications\Activated;
use App\Notifications\PasswordReset;
use App\Repositories\UserRepository;
use App\Notifications\PasswordResetted;
use App\Repositories\IpFilterRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\ConfigurationRepository;
use App\Repositories\LoginThrottleRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\TwoFactorSecurityRepository;

class AuthRepository
{
    protected $user;
    protected $throttle;
    protected $two_factor;
    protected $config;
    protected $ip_filter;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $user,
        LoginThrottleRepository $throttle,
        TwoFactorSecurityRepository $two_factor,
        ConfigurationRepository $config,
        IpFilterRepository $ip_filter
    ) {
        $this->user       = $user;
        $this->throttle   = $throttle;
        $this->two_factor = $two_factor;
        $this->config     = $config;
        $this->ip_filter  = $ip_filter;
    }

    /**
     * Authenticate an user.
     *
     * @param array $params
     * @return array
     */
    public function auth($params = array())
    {
        $this->throttle->validate();

        $token = $this->validateLogin($params);

        $auth_user = $this->user->findByEmail($params['email']);

        $this->validateStatus($auth_user);

        event(new UserLogin($auth_user));

        $two_factor_code = $this->two_factor->set($auth_user);

        return [
            'token'           => $token,
            'user'            => $auth_user,
            'two_factor_code' => $two_factor_code
        ];
    }

    /**
     * Validate login credentials.
     *
     * @param array $params
     * @return auth token
     */
    public function validateLogin($params = array())
    {
        $email = isset($params['email']) ? $params['email'] : null;
        $password = isset($params['password']) ? $params['password'] : null;

        try {
            if (! $token = \JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                $this->throttle->update();

                throw ValidationException::withMessages(['email' => trans('auth.failed')]);
            }
        } catch (JWTException $e) {
            throw ValidationException::withMessages(['email' => trans('general.something_wrong')]);
        }

        $this->throttle->clearCache();

        return $token;
    }

    /**
     * Validate authenticated user status.
     *
     * @param authenticated user
     * @return null
     */
    public function validateStatus($auth_user)
    {
        if ($auth_user->status === 'pending_activation') {
            throw ValidationException::withMessages(['email' => trans('auth.pending_activation')]);
        }

        if ($auth_user->status === 'pending_approval') {
            throw ValidationException::withMessages(['email' => trans('auth.pending_approval')]);
        }

        if ($auth_user->status === 'disapproved') {
            throw ValidationException::withMessages(['email' => trans('auth.not_activated')]);
        }

        if ($auth_user->status === 'banned') {
            throw ValidationException::withMessages(['email' => trans('auth.account_banned')]);
        }

        if ($auth_user->status != 'activated') {
            throw ValidationException::withMessages(['email' => trans('auth.not_activated')]);
        }

        if (!$auth_user->hasPermissionTo('enable-login')) {
            throw ValidationException::withMessages(['email' => trans('auth.login_permission_disabled')]);
        }
    }

    /**
     * Validate auth token.
     *
     * @return array
     */
    public function check()
    {
       
        $public_config = config('system.public_config');
        foreach (config('system.social_login_providers') as $value) {
            array_push($public_config, $value.'_login');
        }

        $configuration = $this->config->getSelectedByName($public_config);

        if (config('config.ip_filter') && $this->ip_filter->count() && ! validateIp($this->ip_filter->getAll())) {
            return ['ip_restricted' => 1,'config' => $configuration];
        }

        try {
            \JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return ['authenticated' => false,'config' => $configuration];
        }

        $authenticated                  = true;
        $configuration                  = $this->config->getAllPublic();
        $configuration['post_max_size'] = getPostMaxSize();
        $configuration['pagination']    = config('system.pagination');
        $auth_user                      = $this->user->findOrFail(\Auth::user()->id);
        $social_auth                    = ($auth_user->password) ? 0 : 1;
        $user_roles                     = $auth_user->getRoleNames();
        $permissions                    = $auth_user->getAllPermissions();
        $default_role                   = config('system.default_role');
        $configuration['l']             = config('config.l');


        $company                      = $auth_user->profile->company;

        return [
            'authenticated' => $authenticated,
            'config'        => $configuration,
            'user'          => $auth_user,
            'company'          => $company,
            'social_auth'   => $social_auth,
            'user_roles'    => $user_roles,
            'permissions'   => $permissions,
            'default_role'  => $default_role
        ];
    }

    /**
     * Check for registration availability.
     *
     * @return null
     */
    public function validateRegistrationStatus()
    {
        if (! config('config.registration')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for email verification availability.
     *
     * @return null
     */
    public function validateEmailVerificationStatus()
    {
        if (! config('config.email_verification')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for account approval availability.
     *
     * @return null
     */
    public function validateAccountApprovalStatus()
    {
        if (! config('config.account_approval')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Check for reset password availability.
     *
     * @return null
     */
    public function validateResetPasswordStatus()
    {
        if (! config('config.reset_password')) {
            throw ValidationException::withMessages(['message' => trans('general.feature_not_available')]);
        }
    }

    /**
     * Activate user's account.
     *
     * @param string $activation token
     * @return null
     */
    public function activate($activation_token = null)
    {
        $this->validateRegistrationStatus();

        $this->validateEmailVerificationStatus();

        $user = $this->user->findByActivationToken($activation_token);

        if (!$user) {
            throw ValidationException::withMessages(['message' => trans('auth.invalid_token')]);
        }

        if ($user->status === 'activated') {
            throw ValidationException::withMessages(['message' => trans('auth.account_already_activated')]);
        }

        if ($user->status != 'pending_activation') {
            throw ValidationException::withMessages(['message' => trans('auth.invalid_token')]);
        }

        $user->status = (config('config.account_approval') ? 'pending_approval' : 'activated');
        $user->save();
        $user->notify(new Activated($user));
    }

    /**
     * Validate user for reset password.
     *
     * @param email $email
     * @return User
     */
    public function validateUserAndStatusForResetPassword($email = null)
    {
        $user = $this->user->findByEmail($email);

        if (! $user) {
            throw ValidationException::withMessages(['email' => trans('passwords.user')]);
        }

        if ($user->status != 'activated') {
            throw ValidationException::withMessages(['email' => trans('passwords.account_not_activated')]);
        }

        return $user;
    }

    /**
     * Request password reset token of user.
     *
     * @param array
     * @return null
     */
    public function password($params = array())
    {
        $this->validateResetPasswordStatus();

        $user = $this->validateUserAndStatusForResetPassword($params['email']);

        $token = Str::uuid();
        \DB::table('password_resets')->insert([
            'email' => $params['email'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $user->notify(new PasswordReset($user, $token));
    }

    /**
     * Validate reset password token.
     *
     * @param string $token
     * @param email $email
     * @return null
     */
    public function validateResetPasswordToken($token, $email = null)
    {
        if ($email) {
            $reset = \DB::table('password_resets')->where('email', '=', $email)->where('token', '=', $token)->first();
        } else {
            $reset = \DB::table('password_resets')->where('token', '=', $token)->first();
        }

        if (! $reset) {
            throw ValidationException::withMessages(['message' => trans('passwords.token')]);
        }

        if (date("Y-m-d H:i:s", strtotime($reset->created_at . "+".config('config.reset_password_token_lifetime')." minutes")) < date('Y-m-d H:i:s')) {
            throw ValidationException::withMessages(['email' => trans('passwords.token_expired')]);
        }
    }

    /**
     * Reset password of user.
     *
     * @param array
     * @return null
     */
    public function reset($params = array())
    {
        $this->validateResetPasswordStatus();
    
        $user = $this->validateUserAndStatusForResetPassword($params['email']);

        $this->validateResetPasswordToken($params['token'], $params['email']);

        $this->resetPassword($params['password'], $user);

        \DB::table('password_resets')->where('email', '=', $params['email'])->where('token', '=', $params['token'])->delete();

        $user->notify(new PasswordResetted($user));
    }

    /**
     * Update user password.
     *
     * @param string $password
     * @param User $user
     * @return null
     */
    public function resetPassword($password, $user = null)
    {
        $user = ($user) ? : \Auth::user();
        $user->password = bcrypt($password);
        $user->save();
    }

    /**
     * Validate current password of user.
     *
     * @param string $password
     * @return null
     */
    public function validateCurrentPassword($password)
    {
        if (!\Hash::check($password, \Auth::user()->password)) {
            throw ValidationException::withMessages(['message' => trans('passwords.password_mismatch')]);
        }
    }
}
