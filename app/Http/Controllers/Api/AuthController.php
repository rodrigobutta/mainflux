<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use JWTAuth;
use Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use IlluminateHttpRequest;
use AppHttpRequests;
use AppHttpControllersController;
use Firebase\Auth\Token\Verifier;

use Carbon\Carbon;
use App\User;


class AuthController extends Controller
{

    private $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('api');        
    }


    
    public function firebaseLogin(Request $request)
    {

        $token = $request->get('token');
        $email = $request->get('email');
        $emailVerified = $request->get('emailVerified');
        $name = $request->get('name');        
        $phone = $request->get('phone');
        $photourl = $request->get('photourl');
        $provider = $request->get('provider');
        $uid = $request->get('uid');

        $firebase_project_id = env('FIREBASE_PROJECT_ID', 'project-111111');
        $verifier = new Verifier($firebase_project_id);
        try {
            $verifiedIdToken = $verifier->verifyIdToken($token);            
            $firebaseUid = $verifiedIdToken->getClaim('sub'); // "a-uid"

            $user = User::where('email',$email)->first();

            if($user){
            
                $token = JWTAuth::fromUser($user);
                
            }
            else{

                $user = new User;
                $user->email = $email;
                $user->status = 'activated';
                $user->activation_token = Str::uuid();
                $user->save();

                $user->assignRole((\App\User::count()) ? config('system.default_role.user') : config('system.default_role.admin'));
                
                $profile = new \App\Profile;
                $user->profile()->save($profile);
                
                $name = explode(' ', $name);
                $profile->provider = $provider;                
                $profile->provider_unique_id = $firebaseUid;
                $profile->first_name = array_key_exists(0, $name) ? $name[0] : 'John';
                $profile->last_name = array_key_exists(1, $name) ? $name[1] : 'Doe';                
                $profile->phone = $phone;
                $profile->avatar = $photourl;
                $profile->save();

                $user_preference = new \App\UserPreference;
                $user->userPreference()->save($user_preference);

                $token = JWTAuth::fromUser($user);

                $user_id = $user->id;
                
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',                
                'user' => $user->jsonMainInfo()
            ]);

        } catch (\Firebase\Auth\Token\Exception\ExpiredToken $e) {
            echo $e->getMessage();
        } catch (\Firebase\Auth\Token\Exception\IssuedInTheFuture $e) {
            echo $e->getMessage();
        } catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
            echo $e->getMessage();
        }

    }

    // /**
    //  * Create user
    //  *
    //  * @param  [string] name
    //  * @param  [string] email
    //  * @param  [string] password
    //  * @param  [string] password_confirmation
    //  * @return [string] message
    //  */
    // public function signup(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|string|email|unique:member',
    //         'password' => 'required|string|confirmed'
    //     ]);
    //     $user = new Member([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password)
    //     ]);

    //     // dd($user->email);


    //     $user->save();
    //     return response()->json([
    //         'message' => 'Successfully created user!'
    //     ], 201);
    // }
  
    // /**
    //  * Login user and create token
    //  *
    //  * @param  [string] email
    //  * @param  [string] password
    //  * @param  [boolean] remember_me
    //  * @return [string] access_token
    //  * @return [string] token_type
    //  * @return [string] expires_at
    //  */
    // public function login(Request $request)
    // {
        
    //     $request->validate([
    //         'email' => 'required|string|email',
    //         'password' => 'required|string',
    //         'remember_me' => 'boolean'
    //     ]);

    //     $user = Member::where('email',$request->get('email'))->first();

    //     if(!$user){
    //         return response()->json([
    //             'message' => 'Member Not Found'
    //         ], 401);
    //     }

    //     if(!password_verify($request->get('password'),$user->password)){
    //         return response()->json([
    //             'message' => 'Unauthorized'
    //         ], 401);
    //     }
        

    //     $tokenResult = $user->createToken('Personal Access Token');
    //     $token = $tokenResult->token;
    //     if ($request->remember_me)
    //         $token->expires_at = Carbon::now()->addYears(2);
    //     $token->save();

    //     return response()->json([
    //         'access_token' => $tokenResult->accessToken,
    //         'token_type' => 'Bearer',
    //         'expires_at' => Carbon::parse(
    //             $tokenResult->token->expires_at
    //         )->toDateTimeString()
    //     ]);

    // }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated Member
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }



}