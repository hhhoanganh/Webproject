<?php

namespace App\Http\Controllers;

use App\Models\User;

use Laravel\Socialite\Facades\Socialite;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class FacebookSocialiteController extends Controller
{

    public function redirectToFB()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            $finduser = User::where('social_id', $user->id)->first();

            if ($finduser) {

                Auth::login($finduser);

                return $this->sendSuccess($finduser);

            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id' => $user->id,
                    'social_type' => 'facebook',
                    'password' => encrypt('my-facebook')
                ]);

                Auth::login($newUser);

                return $this->sendSuccess($newUser);
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
