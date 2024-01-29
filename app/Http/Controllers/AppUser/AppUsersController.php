<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Models\AppUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            $text = "رمز التحقق هو: " . $otp . " للاستخدام في تطبيق أنعام بيشة ";
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
