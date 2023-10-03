<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Validator;

// User models
use App\Models\User;

// PHPMailer lib
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RegisterController extends Controller
{
    // Mail function
	private function sendEmail($data, $attachment = false, $filename = '')
    {
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0; // Enable verbose debug output
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = env('MAIL_HOST'); // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = env('MAIL_USERNAME'); // SMTP username
            $mail->Password = env('MAIL_PASSWORD'); // SMTP password
            $mail->SMTPSecure = env('MAIL_ENCRYPTION'); // Enable TLS encryption, `ssl` also accepted
            $mail->Port = env('MAIL_PORT');

            $mail->setFrom(env('MAIL_USERNAME'), $data['sender_name']);
            $mail->addAddress($data['receiver_email'], $data['receiver_name']);     // Add a recipient
            $mail->addBCC(env('MAIL_USERNAME'));
            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body    = $data['body'];
            $mail->AltBody = $data['body_alt'];

            if ($attachment) {
                $mail->addAttachment($attachment, $filename);
            }

            if ($mail->send()) {
                return true;
            } else {
                \Log::error('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
                return false;
            }
        } catch (\Throwable $th) {
            \Log::error('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public function newregister(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'username' => 'required',
            'name' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:3|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse('n', 'Error validation', $validator->errors());
        }

        $userName = $input['username'];
        $name = $input['name'];
        $email = $input['email'];
        $pwd = Hash::make($input['password']);

        $arrData = [
            "username" => $userName,
            "name" => $name,
            "email" => $email,
            "pwd" => $pwd,
        ];

        $mailSent = false;
        $linkAktivasi = '';

        $registrasiData = User::create($arrData);
        $userID = Crypt::encryptString($registrasiData->id);

        // generate signed url
        $linkAktivasi = URL::temporarySignedRoute(
            'verifymail', now()->addMinutes(30), ['userid' => $userID]
        );

        $isimail = "Klik link berikut untuk aktivasi username ".$linkAktivasi;
        $isimail_nohtml = "Klik link berikut untuk aktivasi username ".$linkAktivasi;
        $subject = "[NEW USER]";
        $send = $this->sendEmail(
            [
                'sender_name' => getenv("MAIL_FROM_NAME"),
                'subject' => $subject.' - '.$userName,
                'receiver_email' => $email,
                'receiver_name' =>  $userName,
                'body' => $isimail,
                'body_alt' => $isimail_nohtml
            ],
        );

        if ($send) {
            $mailSent = true;
        } else {
            \Log::error(['Error ' => 'Email Errors']);
        }
    
        $retData = [
            "mailSent" => $mailSent,
            "registrasiData" => $arrData,
            "linkAktivasi" => $linkAktivasi
        ];
        return $this->sendResponse('y', $retData, 'New User Created');
    }

    public function verifymail(Request $request){
        $encryptedUserid = $request->input('userid');
        $userid = Crypt::decryptString($encryptedUserid);

        $retData = [
            "encryptedUserid" => $encryptedUserid,
            "userid" => $userid
        ];

        User::where('id', $userid)->update(["email_verified_at" => now()]);

        return $this->sendResponse('y', $retData, 'Userid '.$userid.' Verified');
    }
}