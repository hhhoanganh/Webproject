<?php

namespace App\Http\Controllers;

use App\Mail\OrderEmail;
use App\Mail\SignupEmail;
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

    public static function sendOrderEmail($name, $email, $verification_code, $order)
    {
        $data = [
            'name' => $name,
            'verification_code' => $verification_code,
            'order' => $order
        ];
        Mail::to($email)->send(new OrderEmail($data));
    }

}
