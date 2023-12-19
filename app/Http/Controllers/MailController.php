<?php

namespace App\Http\Controllers;

use App\Mail\SignupEmail;
use App\Mail\StatusEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public static function sendSignUpEmail($name, $email, $verification_code)
    {
        $data = [
            'name' => $name,
            'verification_code' => $verification_code
        ];
        Mail::to($email)->send(new SignupEmail($data));
    }

    public static function sendSuccessEmail($code, $email, $message ){
        if ($message != 'success'){
            Mail::to($email)->send(new StatusEmail($code, true));
        }
        Mail::to($email)->send(new StatusEmail($code, false));
    }

}
