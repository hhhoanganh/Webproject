<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'phone' => '|min:10|max:11',
        ], [
            'phone.min' => 'Trường điện thoại có ít nhất 10 số',
            'phone.max' => 'Trường điện thoại có ít hơn 12 số',
        ]);
        $profile = Auth::user();
        $profile->name = $request->get('name');
        $profile->phone = $request->get('phone');
        $profile->address = $request->get('address');
        $profile->save();
        return $this -> sendSuccess($profile, 'Bạn đã cập nhật thông tin thành công');
    }

    public function getProfile(){
        $user = Auth::getUser();
        return $this->sendSuccess($user);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            "current_password" => "required",
            "password" => "required|confirmed|min:4",
            "password_confirmation" => "required|min:4",
        ]);
        $user = Auth::user();
        if (!(Hash::check($request->current_password, $user->password))) {
            $message = "Current password is wrong!";
            return $this->sendError($message);
        } else if ($request->current_password == $request->password) {
            $message = "Current password cannot be the same as new password!";
            return $this->sendError($message);
        }

        User::where('id', $user->id)
            ->update(['password' => Hash::make($validated['password'])]);

        $message = "Password has been updated";
        return $this->sendSuccess($user,null,$message);
    }
}
