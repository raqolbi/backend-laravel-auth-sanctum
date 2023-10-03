<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

// User models
use App\Models\User;
use App\Models\UserSession;

class AuthController extends Controller
{
    public function login(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse('n', 'Error validation', $validator->errors());
        }

        $userName = $input['username'];
        
        // check database
        $userData = User::where("username", $userName)->whereNotNull("email_verified_at")->first();

        if (empty($userData->id) || !Hash::check($input['password'], $userData->pwd)) {
            return $this->sendResponse('n', 'Unauthorized', ['messages' => 'Username/Password invalid!']);
        }

        $userid = $userData->id;

        $arrData = [
            "userid" => $userid,
            "login_at" => now()
        ];
        $loginData = UserSession::create($arrData);
        
        // token sanctum
        $token = $loginData->createToken('user_auth')->plainTextToken;
    
        $retData = [
            "userid" => $userid,
            "sanctumToken" => $token
        ];
        return $this->sendResponse('y', $retData, 'User logged in');
    }

    public function logout(Request $request){
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $tokenData = PersonalAccessToken::findToken($token);
		$sessionID = $tokenData->tokenable_id; // ID Session used in tokenable_id in personal_access_tokens

        $arrData = [
            "logout_at" => now()
        ];
        UserSession::where("id", $sessionID)->update($arrData);
        $request->user()->currentAccessToken()->delete(); // revoke token
        $retData = [];
        return $this->sendResponse('y', $retData, 'User logged out');
    }

    public function authtest(Request $request){
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $tokenData = PersonalAccessToken::findToken($token);
		$sessionID = $tokenData->tokenable_id; // ID Session used in tokenable_id in personal_access_tokens

        $retData = [
            "sessionID" => $sessionID
        ];
        return $this->sendResponse('y', $retData, 'User Session Valid');
    }
}