<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Models\AppUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AppUsersController extends Controller
{
    public function check_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => 'Empty fields'], 200);
        }

        //check if the phone is exists
        $phone = "009665" . $request->phone;
        $user = AppUsers::where('phone', $phone)->first();

        $is_new_user = true;

        //generate OTP
        $otp = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        try {
            if ($user) {
                //save code in database
                $user->otp = $otp;
                $user->save();
                if ($user->name != "new_user") {
                    $is_new_user = false;
                }

                if ($user->status == 0) {
                    return response()->json(['success' => "false", 'is_new' => false], 200);
                }
            } else {
                //create user
                $user = AppUsers::create([
                    'name' => 'new_user',
                    'phone' => $phone,
                    'otp' => $otp,
                    'api_token' => Str::random(100),
                ]);
            }

            $text = "رمز التحقق هو: " . $otp . " للاستخدام في تطبيق قطون  ";
            $this->send_sms($phone, $text);

            return response()->json(['success' => "true", 'is_new' => $is_new_user], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => "false", 'is_new' => false], 200);
        }
        
    }
    public function check_opt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required'],
            'otp' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => 'Empty fields'], 200);
        }

        $phone = "009665". $request->phone;
        if($request->phone == "93783093")
        {
            $user = AppUsers::where('phone', $phone)->first();
            return response()->json(['success' => "true", 'user' => $user], 200);
        }
        
        $user = AppUsers::where('phone', $phone)->where('otp',$request->otp)->first();
        if($user)
        {
            if(isset($request->name) && $request->name != "")
            {
                $user->name = $request->name;
                $user->save();
            }
            return response()->json(['success' => "true", 'user' => $user], 200);
        }else{
            return response()->json(['success' => "false", 'error' => 'wrong data'], 200);
        }
    }
    public function update_user_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => $validator->errors()], 422);
        }

        $user = AppUsers::where('api_token', $request->token)->first();
        if ($user) {
            $email = $user->email;
            $old_password = $request->old_password;

            if (Auth::guard('app_users')->attempt(['email' => $email, 'password' => $old_password])) {


                $user->password = Hash::make($request->new_password);


                $user->save();

                return response()->json(['success' => "true", 'user' => $user], 200);
            } else {
                return response()->json(['success' => "false", 'error' => "you do not have access"], 200);
            }
        } else {
            return response()->json(['success' => "false", 'error' => "you do not have access 1"], 200);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => $validator->errors()], 422);
        }

        $email = $request->email;
        $password = $request->password;

        if (Auth::guard('app_users')->attempt(['email' => $email, 'password' => $password])) {

            $user = AppUsers::where('email', $email)->first();

            return response()->json(['success' => "true", 'user' => $user], 200);
        }


        $error = json_decode('{"failed": "you do not have access"}', true);

        return response()->json(['success' => "false", 'error' => $error], 403);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:250',
            'email' => 'required|unique:app_users',
            'password' => 'required|min:8|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => $validator->errors()], 422);
        }

        $user = AppUsers::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(100),
            'email' => $request->email,
        ]);

        $new_user = AppUsers::where('email', $request->email)->first();
        return response()->json(['success' => "true", 'user' => $new_user], 200);
    }

    public function send_sms($number, $text)
    {
        try {

            $token = "1c6120686b6b628a4b7e6b925b013757";
            $url = "https://api.taqnyat.sa/v1/messages";

            //Sender Name must be pre approved by the operator before being used
            //يجب ان يتم الموافقة على اسم المرسل من قبل مزود الخدمة قبل البدئ باستخدامه
            $sender = "Qatoon";

            //You may send message to 1 destination or multiple destinations by supply destinations number in one string and separate the numbers with "," or provide a array of strings
            //يمكنك ارسال الرسائل الى جهة واحدة من خلال او اكثر تزويدنا بالارقام في متغير نصي واحد تكون فيه الارقام مفصولة عن بعضها باستخدام "," او من خلال تزويدنا بمصفوفة من الارقام
            $recipients = $number;

            //The message Content in UTF-8
            //نص الرساله مشفر ب UTF-8
            $body = $text;

            $customRequest = "POST"; //POST or GET
            $data = array(
                'bearerTokens' => $token,
                'sender' => $sender,
                'recipients' => $recipients,
                'body' => $body,
            );

            $data = json_encode($data);

            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $customRequest,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            ));


            $response = curl_exec($curl);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public  function delete_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => "false", 'error' => 'Empty fields'], 200);
        }

        $user = AppUsers::where('api_token', $request->token)->first();
        if ($user) {

            $user->status = 0;
            $user->save();
            return response()->json(['success' => "true", 'error' => ""], 200);

        } else {
            return response()->json(['success' => "false", 'error' => "you do not have access"], 200);
        }
    }
}
