<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendError($message,$option = [])
    {
        return Response::json([
            'status' => data_get($option, "status", "error"),
            'error_code' => data_get($option, "error_code", 1),
            'message' => $message,
            'data' => data_get($option, "data", [])
        ], data_get($option, "status_code",400));
    }

    public function sendSuccess($data = [], $meta = [], $message = "success")
    {
            return Response::json([
                'status' => AppConstant::SUCCESS_CODE,
                'error_code' => 0,
                'message' => $message,
                'data' => $data,
                'meta' => $meta
            ]);
    }
}
