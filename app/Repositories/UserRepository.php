<?php
namespace App\Repositories;

use App\User;
use App\Profile;
use App\Jobs\SendMail;
use App\UserPreference;
use Illuminate\Support\Str;
use App\Notifications\Activation;
use Illuminate\Support\Facades\Log;
use App\Repositories\RoleRepository;
use App\Repositories\EmailLogRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use App\Repositories\DesignationRepository;
use Illuminate\Validation\ValidationException;

class UserRepository
{
    protected $user;
    protected $role;
    protected $email;
    protected $designation;
    protected $location;
    protected $client;
    protected $contractor;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        User $user,
        RoleRepository $role,
        EmailLogRepository $email,
        DesignationRepository $designation,
        LocationRepository $location,
        ClientRepository $client,
        ContractorRepository $contractor
    ) {
        $this->user  = $user;
        $this->role  = $role;
        $this->email = $email;
        $this->designation = $designation;
        $this->location = $location;
        $this->client = $client;
        $this->contractor = $contractor;
    }

    /**
     * Get all users with profile
     *
     * @return User
     */

    public function getAll()
    {
        return $this->user->with('profile', 'roles')->get();
    }

    /**
     * List all users by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->user->all()->pluck('id')->all();
    }

    /**
     * Count users
     *
     * @return integer
     */

    public function count()
    {
        return $this->user->count();
    }

    /**
     * Count users registered between dates
     *
     * @return integer
     */

    public function countDateBetween($start_date, $end_date)
    {
        return $this->user->createdAtDateBetween(['start_date' => $start_date, 'end_date' => $end_date])->count();
    }

    /**
     * Find user by Id
     *
     * @param integer $id
     * @return User
     */

    public function findOrFail($id = null)
    {
        $user = $this->user->with('profile', 'roles', 'profile.designation', 'profile.designation.department', 'profile.location', 'profile.client', 'profile.contractor')->find($id);

        if (! $user) {
            throw ValidationException::withMessages(['message' => trans('user.could_not_find')]);
        }

        return $user;
    }

    /**
     * Find user by Email
     *
     * @param email $email
     * @return User
     */

    public function findByEmail($email = null)
    {
        return $this->user->with('profile', 'roles', 'profile.designation', 'profile.designation.department', 'profile.location', 'profile.client', 'profile.contractor')->filterByEmail($email)->first();
    }

    /**
     * Find user by activation token
     *
     * @param string $token
     * @return User
     */

    public function findByActivationToken($token = null)
    {
        return $this->user->with('profile', 'roles', 'profile.designation', 'profile.designation.department', 'profile.location', 'profile.client', 'profile.contractor')->whereActivationToken($token)->first();
    }

    /**
     * List user except authenticated user by name & email
     *
     * @param string $token
     * @return User
     */

    public function listByNameAndEmailExceptAuthUser()
    {
        return $this->user->where('id', '!=', \Auth::user()->id)->get()->pluck('name_with_email', 'id')->all();
    }

    public function listByNameWithDesignationForSelectedId($user_id = array())
    {
        return $this->user->whereIn('id', $user_id)->get()->pluck('name_with_designation_and_department', 'id')->all();
    }

    /**
     * Paginate all todos using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params = array())
    {
        $sort_by               = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order                 = isset($params['order']) ? $params['order'] : 'desc';
        $page_length           = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $first_name            = isset($params['first_name']) ? $params['first_name'] : null;
        $last_name             = isset($params['last_name']) ? $params['last_name'] : null;
        $email                 = isset($params['email']) ? $params['email'] : null;
        $role_id               = isset($params['role_id']) ? $params['role_id'] : null;
        $designation_id        = isset($params['designation_id']) ? $params['designation_id'] : null;
        $location_id           = isset($params['location_id']) ? $params['location_id'] : null;
        $client_id           = isset($params['client_id']) ? $params['client_id'] : null;
        $contractor_id           = isset($params['contractor_id']) ? $params['contractor_id'] : null;        
        $status                = isset($params['status']) ? $params['status'] : null;
        $created_at_start_date = isset($params['created_at_start_date']) ? $params['created_at_start_date'] : null;
        $created_at_end_date   = isset($params['created_at_end_date']) ? $params['created_at_end_date'] : null;

        $accessible_users = $this->getAccessibleUserId();
        $query = $this->user->with('profile', 'roles', 'profile.designation', 'profile.designation.department', 'profile.location', 'profile.client', 'profile.contractor')->whereIn('id', $accessible_users)->filterByFirstName($first_name)->filterByLastName($last_name)->filterByEmail($email)->filterByDesignationId($designation_id)->filterByLocationId($location_id)->filterByClientId($client_id)->filterByContractorId($contractor_id)->filterByRoleId($role_id)->filterByStatus($status)->createdAtDateBetween([
            'start_date' => $created_at_start_date,
            'end_date' => $created_at_end_date
        ]);

        if ($sort_by === 'first_name') {
            $query->select('users.*', \DB::raw('(select first_name from profiles where users.id = profiles.user_id) as first_name'))->orderBy('first_name', $order);
        } elseif ($sort_by === 'last_name') {
            $query->select('users.*', \DB::raw('(select last_name from profiles where users.id = profiles.user_id) as last_name'))->orderBy('last_name', $order);
        } elseif ($sort_by === 'designation') {
            $query->select('users.*', \DB::raw('(select designation_id from profiles where users.id = profiles.user_id) as designation_id'))->orderBy('designation_id', $order);
        } elseif ($sort_by === 'location') {
            $query->select('users.*', \DB::raw('(select location_id from profiles where users.id = profiles.user_id) as location_id'))->orderBy('location_id', $order);
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Get all user query who are accessible for given user id
     *
     * @param integer $user_id
     * @param boolean $self (Pass 1 to include given user id)
     * @return Query
     */
    public function getAccessibleUser($user_id = '', $self = 0)
    {
        $user_id = ($user_id) ? : \Auth::user()->id;

        $user = $this->user->find($user_id);
        $self = ($user->hasRole(config('system.default_role.admin')) || $self) ? 1 : 0;

        if ($user->hasRole(config('system.default_role.admin'))) {
            return $this->user->with('profile', 'profile.designation', 'profile.designation.department');
        }

        if ($self) {
            $query = $this->user->with('profile', 'profile.designation', 'profile.designation.department')->where(function ($qry1) use ($user) {
                $qry1->where('id', '=', \Auth::user()->id)->orWhereHas('profile', function ($qry) use ($user) {
                    $qry->whereIn('designation_id', $this->designation->getSubordinate($user));
                });
            });
        } else {
            $query = $this->user->with('profile', 'profile.designation', 'profile.designation.department')->whereHas('profile', function ($qry) use ($user) {
                $qry->whereIn('designation_id', $this->designation->getSubordinate($user));
            });
        }

        $location_users = array();
        if (!config('config.location_subordinate_level')) {
            $location_users = \App\User::with('profile', 'profile.designation', 'profile.designation.department')->whereHas('profile', function ($qry) use ($user) {
                $qry->whereLocationId($user->Profile->location_id);
            })->get()->pluck('id')->all();
            $query->whereIn('id', $location_users);
        }

        return $query;
    }

    /**
     * Get all user's id who are accessible for given user id
     *
     * @param integer $user_id
     * @param boolean $self (Pass 1 to include given user id)
     * @return array
     */
    public function getAccessibleUserId($user_id = '', $self = 0)
    {
        return $this->getAccessibleUser($user_id, $self)->get()->pluck('id')->all();
    }

    /**
     * Create a new user.
     *
     * @param array $params
     * @return User
     */

    public function create($params, $register = 0)
    {
        $this->validateInputId($params);

        $user = $this->user->forceCreate($this->formatParams($params, 'register'));

        $role_id = ($register) ? ($this->role->findByName(config('system.default_role.'.($this->count() > 1 ? 'user' : 'admin')))->id) : (isset($params['role_id']) ? $params['role_id'] : null);

        $this->assignRole($user, $role_id);

        $profile = $this->associateProfile($user);

        if ($register) {
            $designation = ($this->count() <= 1) ? $this->designation->getHidden() : $this->designation->getDefault();
            $location = $this->location->getDefault();            
            $profile->designation_id = $designation ? $designation->id : null;
            $profile->location_id = $location ? $location->id : null;
            $profile->save();
        } else {
            $profile = $this->updateProfileRelation($profile, $params);
        }

        $this->updateProfile($profile, $params);

        if (isset($params['send_email']) && isset($params['send_email']) && $params['send_email']) {
            SendMail::dispatch($user->email, [
                'slug'      => 'welcome-email-user',
                'user'      => $user,
                'password'  => $params['password'],
                'module'    => 'user',
                'module_id' => $user->id
            ]);
        }

        if ($register && config('config.email_verification')) {
            $user->notify(new Activation($user));
        }

        return $user;
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $designation_ids = $this->designation->listId();
        $location_ids = $this->location->listId();
        $client_ids = $this->client->listId();
        $contractor_ids = $this->contractor->listId();
        $role_ids = $this->role->listId();

        $designation_id = isset($params['designation_id']) ? $params['designation_id'] : null;
        $location_id = isset($params['location_id']) ? $params['location_id'] : null;
        $client_id = isset($params['client_id']) ? $params['client_id'] : null;
        $contractor_id = isset($params['contractor_id']) ? $params['contractor_id'] : null;
        $role_id = isset($params['role_id']) ? $params['role_id'] : [];

        if ($designation_id && ! in_array($designation_id, $designation_ids)) {
            throw ValidationException::withMessages(['message' => trans('designation.could_not_find')]);
        }

        if ($location_id && ! in_array($location_id, $location_ids)) {
            throw ValidationException::withMessages(['message' => trans('location.could_not_find')]);
        }

        if ($client_id && ! in_array($client_id, $client_ids)) {
            throw ValidationException::withMessages(['message' => trans('client.could_not_find')]);
        }

        if ($contractor_id && ! in_array($contractor_id, $contractor_ids)) {
            throw ValidationException::withMessages(['message' => trans('contractor.could_not_find')]);
        }

        if ($role_id && (! is_array($role_id) || count(array_diff($role_id, $role_ids)))) {
            throw ValidationException::withMessages(['message' => trans('role.could_not_find')]);
        }
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $action
     * @return array
     */

    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'email'       => isset($params['email']) ? $params['email'] : null,
            'status' => 'activated',
            'password' => isset($params['password']) ? bcrypt($params['password']) : null,
            'activation_token' => Str::uuid(),
            'uuid' => Str::uuid()
        ];

        if ($action === 'register') {
            if (config('config.email_verification')) {
                $formatted['status'] = 'pending_activation';
            } elseif (config('config.account_approval')) {
                $formatted['status'] = 'pending_approval';
            }
        }

        return $formatted;
    }

    /**
     * Assign role to user.
     *
     * @param User
     * @param integer $role_id
     * @return null
     */

    private function assignRole($user, $role_id, $action = 'attach')
    {
        if ($action === 'attach') {
            $user->assignRole($this->role->listNameById($role_id));
        } else {
            $user->roles()->sync($role_id);
        }
    }

    /**
     * Associate user to profile.
     *
     * @param User
     * @return Profile
     */

    private function associateProfile($user)
    {
        $profile = new Profile;
        $user->profile()->save($profile);

        $user_preference = new UserPreference;
        $user->userPreference()->save($user_preference);

        return $profile;
    }

    /**
     * Update user profile.
     *
     * @param Profile
     * @param array $params
     * @return null
     */

    public function updateProfile($profile, $params = array())
    {
        $profile->first_name          = isset($params['first_name']) ? $params['first_name'] : $profile->first_name;
        $profile->last_name           = isset($params['last_name']) ? $params['last_name'] : $profile->last_name;
        $profile->address_line_1      = isset($params['address_line_1']) ? $params['address_line_1'] : $profile->address_line_1;
        $profile->address_line_2      = isset($params['address_line_2']) ? $params['address_line_2'] : $profile->address_line_2;
        $profile->city                = isset($params['city']) ? $params['city'] : $profile->city;
        $profile->state               = isset($params['state']) ? $params['state'] : $profile->state;
        $profile->zipcode             = isset($params['zipcode']) ? $params['zipcode'] : $profile->zipcode;
        $profile->country_id          = isset($params['country_id']) ? $params['country_id'] : $profile->country_id;
        $profile->gender              = isset($params['gender']) ? $params['gender'] : $profile->gender;
        $profile->phone               = isset($params['phone']) ? $params['phone'] : $profile->phone;
        $profile->date_of_birth       = isset($params['date_of_birth']) ? ($params['date_of_birth'] ? : null) : $profile->date_of_birth;
        $profile->date_of_anniversary = isset($params['date_of_anniversary']) ? ($params['date_of_anniversary'] ? : null) : $profile->date_of_anniversary;
        $profile->facebook_profile    = isset($params['facebook_profile']) ? $params['facebook_profile'] : $profile->facebook_profile;
        $profile->twitter_profile     = isset($params['twitter_profile']) ? $params['twitter_profile'] : $profile->twitter_profile;
        $profile->linkedin_profile    = isset($params['linkedin_profile']) ? $params['linkedin_profile'] : $profile->linkedin_profile;
        $profile->google_plus_profile = isset($params['google_plus_profile']) ? $params['google_plus_profile'] : $profile->google_plus_profile;
        $profile->save();
    }

    /**
     * Update user profile with designation, location.
     *
     * @param Profile
     * @param array $params
     * @return profile
     */
    public function updateProfileRelation($profile, $params)
    {
        $profile->designation_id = isset($params['designation_id']) ? $params['designation_id'] : null;
        $profile->location_id = (isset($params['location_id']) && $params['location_id']) ? $params['location_id'] : null;
        $profile->client_id = (isset($params['client_id']) && $params['client_id']) ? $params['client_id'] : null;
        $profile->contractor_id = (isset($params['contractor_id']) && $params['contractor_id']) ? $params['contractor_id'] : null;

        $profile->save();

        return $profile;
    }

    /**
     * Update given user.
     *
     * @param User $user
     * @param array $params
     *
     * @return User
     */
    public function update(User $user, $params = array())
    {
        $this->validateInputId($params);

        if (! $user->hasRole(config('system.default_role.admin'))) {
            $this->updateProfileRelation($user->Profile, $params);
        }

        $this->updateProfile($user->Profile, $params);

        if (isset($params['role_id'])) {
            $this->assignRole($user, $params['role_id'], 'sync');
        }

        return $user;
    }

    /**
     * Update given user status.
     *
     * @param User $user
     * @param string $status
     *
     * @return User
     */
    public function status(User $user, $status = null)
    {
        if (!in_array($status, ['activated','pending_activation','pending_approval','banned','disapproved'])) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $user->status = $status;
        $user->save();

        return $user;
    }

    /**
     * Force reset user password.
     *
     * @param User $user
     * @param string $password
     *
     * @return User
     */
    public function forceResetPassword(User $user, $password = null)
    {
        $user->password = bcrypt($password);
        $user->save();

        return $user;
    }

    /**
     * Send email to user.
     *
     * @param User $user
     * @param array $params
     *
     * @return null
     */
    public function sendEmail(User $user, $params = array())
    {
        $body = isset($params['body']) ? $params['body'] : null;
        $subject = isset($params['subject']) ? $params['subject'] : null;
        $email = $user->email;

        \Mail::send('emails.email', compact('body'), function ($message) use ($subject, $email) {
            $message->to($email)->subject($subject);
        });

        $this->email->record([
            'to' => $email,
            'subject' => $subject,
            'body' => $body,
            'module' => 'user',
            'module_id' => $user->id
        ]);
    }

    /**
     * Find user & check it can be deleted or not.
     *
     * @param integer $id
     * @return User
     */
    public function deletable($id)
    {
        $user = $this->findOrFail($id);

        if ($user->Job->count()) {
            throw ValidationException::withMessages(['message' => trans('user.has_many_jobs')]);
        }
        
        return $user;
    }

    /**
     * Delete user.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(User $user)
    {
        return $user->delete();
    }

    /**
     * Delete multiple users.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->user->whereIn('id', $ids)->delete();
    }
}
