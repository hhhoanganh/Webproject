<?php

namespace App\Http\Controllers;

use App\Models\Authenication\Enum\RoleEnum;
use App\Models\Authenication\Enum\Status;
use App\Models\Authenication\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','verifyAccount']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            // Check if the email exists in the database
            return $this->sendError("Unauthorized",AppConstant::UNAUTHORIZED_CODE);
        }
        $userExists = User::where('email', $request->email)->exists();
        if (!$userExists) {
            return $this->sendError("Email not found.", AppConstant::NOT_FOUND_CODE);
        }
        $user = Auth::user();
        return $this->sendSuccess(
            [
                'user' => $user,
                'token' => $token,
                'authorization' => [
                    'type' => 'bearer',
                ]
            ], null,
            "Login Successful!");
    }

    public function generateToken()
    {
        return sha1(time());
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:20',
            'name' => 'required',
            're_password' => 'required|same:password',
            'phone' => 'required|min:10|max:11'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Không đúng định dạng email',
            'email.unique' => 'Email đã có người sử dụng vui vòng nhập email khác',
            'password.required' => 'Vui lòng nhập mật khẩu',
            're_password.same' => 'Mật khẩu không giống nhau, mời bạn nhập lại !',
            'password.min' => 'Mật khẩu ít nhất phải có 6 kí tự',
            'password.max' => 'Mật khẩu ít phải ít hơn 20 kí tự',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.min' => 'Trường điện thoại có ít nhất 10 số',
            'phone.max' => 'Trường điện thoại có ít hơn 12 số',
            'address.required' => 'Vui lòng nhập địa chỉ của bạn',
            'name.required' => 'Vui lòng nhập tên',
            're_password.required' => 'Vui lòng nhập lại mật khẩu',
        ]);
        $role = Role::where('name', RoleEnum::CUSTOMER)->first();
        $token = $this->generateToken();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => Status::INACTIVE,
            'verification_code' => $token,
            'token_expires_at' => Carbon::now()->addMinutes(5)
        ]);
        $role->users()->attach($user);
        if ($user->status == Status::INACTIVE) {
            MailController::sendSignUpEmail($user->name, $user->email, $token);
        }
        return $this->sendSuccess(
            ['token_verify'=>$token],
            "Please Check Your Email To Active Account");
    }

    public function logout()
    {
        Auth::logout();
        return $this->sendSuccess([],"Successful logout");
    }

    public function verifyAccount(Request $request)
    {
        $token = $request->get('token');
        $user = User::where('verification_code',$token)->first();
        if ($user == null) {
            return $this->sendError("Token is invalid");
        }
        // Check if the account is not already active
        if ($user->status !== Status::ACTIVE) {
            if ($user->token_expires_at->isFuture()) {
                $user->status = Status::ACTIVE;
                $user->delete($token);
                $user->save();
                return $this->sendSuccess([], "Account is active");
            }
            $user->delete();
            return $this->sendError("Account is already active");
        }
        return $this->sendError("Token is invalid or expired");
    }
}
