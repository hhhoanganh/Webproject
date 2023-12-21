<?php

namespace App\Http\Controllers;

use App\Models\Authenication\Enum\RoleEnum;
use App\Models\Authenication\Enum\Status;
use app\Models\Authenication\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class FacebookSocialiteController extends Controller
{
    public function redirectToFB()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();
            $findUser = User::where('social_id', $user->id)->first();

            if ($findUser) {
                $token = JWTAuth::fromUser($findUser);
                $user = Auth::user();
                    return $this->sendSuccess([
                        'user' => $user,
                        'token' => $token,
                        'authorization' => [
                            'type' => 'bearer'
                        ]
                    ], null, "Login Successful!");

            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id' => $user->id,
                    'social_type' => 'facebook',
                    'password' => bcrypt('my-facebook'), // Use bcrypt for password hashing
                    'status' => Status::ACTIVE,
                ]);
                $role = Role::where('id', 3)->first();
                $newUser->roles()->attach($role);
                $newUser->save();
                $token = JWTAuth::fromUser($findUser);
                $user = Auth::user();
                return $this->sendSuccess([
                    'user' => $user,
                    'token' => $token,
                    'authorization' => [
                        'type' => 'bearer',
                    ]
                ], null, "Login Successful!");
            }

        } catch (Exception $e) {
            // Log the error or handle it appropriately
            return $this->sendError("Error occurred", AppConstant::INTERNAL_SERVER_ERROR_CODE);
        }
    }
}
