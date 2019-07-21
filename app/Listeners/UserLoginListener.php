<?php

namespace App\Listeners;

use JWTAuth;
use App\Events\UserLogin;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoginListener
{
    protected $init;

    /**
     * To check logged in user has profile & user preferences associated with it
     *
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
       
        $user = $event->user;

        $profile = $user->Profile;

        if (!isset($profile) && $profile === '' && $profile === null) {
            $profile = new \App\Profile;
            $profile->user()->associate($user);
            $profile->save();
        }

        $user_preference = $user->UserPreference;

        if (!isset($user_preference) && $user_preference === '' && $user_preference === null) {
            $user_preference = new \App\UserPreference;
            $user_preference->user()->associate($user);
            $user_preference->save();
        }
    }
}
