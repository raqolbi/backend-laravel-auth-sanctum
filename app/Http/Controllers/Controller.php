<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function sendResponse($status, $result, $message, $code = 200)
    {
        $response = [
            'status' => $status,
            'data'    => $result,
            'note' => $message,
        ];
        return response()->json($response, $code);
    }
}
