<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('email', 'password');
            $token = Auth::attempt($credentials);

            if (!$token) {
                // Check if the email exists in the database
                $userExists = User::where('email', $credentials['email'])->exists();

                if (!$userExists) {
                    return $this->sendError("Email not found.",AppConstant::NOT_FOUND_CODE);
                }

                return $this->sendError("Incorrect password.",AppConstant::UNAUTHORIZED_CODE);
            }
            $user = Auth::user();
            return $this->sendSuccess(
                $user,
                "Login Successful!");
        } catch (Exception $e) {
            return $this->sendError($e);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
            'phone' => 'required|String',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone'=> $request->phone,
            'role_id' => Role::CUSTOMER_ID,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function logout()
    {
        try {
            Auth::logout();
            return $this->sendSuccess([],"Successful logout");
        } catch (Exception $e){
            return $this->sendError($e);
        }

    }


    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
