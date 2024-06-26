<?php

namespace App\Http\Controllers\AppUser;

use App\Http\Controllers\Controller;
use App\Models\AppUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{


// public function login(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'email' => 'required|email',
//         'password' => 'required|string|min:6',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             "status" =>  false,
//             "message" =>  $validator->errors(),
//             "response" => null,
//         ]);
//     }

//     if (!$token = auth()->attempt($validator->validated())) {
//         return response()->json([
//             "status" =>  false,
//             "message" => "من فضلك تاكد من البريد الالكتروني او كلمة المرور",
//             "response" => null,
//         ]);
//     }

//     $user = auth()->user();
//     $id = $user->id;
//     $name = $user->name;
//    $roles = $user->roles;

//     return response()->json([
//         "id" => $id,
//         "status" => true,
//         "message" => "تم تسجيل الدخول بنجاح",
//         "access_token" => $token,
//         "roles" => $roles,
//         "name" => $name,
//     ]);
// }
public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->guard('app_users')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
        $user = auth()->guard('app_users')->user();
        $id = $user->id;
        $name = $user->name;

        return response()->json([
            'access_token' => $token,
            "data" => $user,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }

  /**
   * Register a User.
   *
   * @return \Illuminate\Http\JsonResponse
   */

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone' => 'required|string|unique:app_users,phone',
            'email' => 'required|string|email|unique:app_users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        $user = AppUsers::create([
            'name' => $request->name,
            'phone' => "009665" . $request->phone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            "data" => $user,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ]);
    }

    /**
     * Log out the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->guard('app_users')->logout();
        return response()->json(['message' => 'عملية تسجيل الخروج تمت بنجاح']);
   }
}
