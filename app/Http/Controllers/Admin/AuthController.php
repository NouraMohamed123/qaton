<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

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

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
        $user = auth()->user();

        $roles = $user->roles;
        $permissions = $roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        });
        return response()->json([
            'access_token' => $token,
            'user' => new UserResource($user),
            "roles" => $roles,
            'permissions' => $permissions,
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
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
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
        auth()->logout();
        return response()->json(['message' => 'عملية تسجيل الخروج تمت بنجاح']);
   }
}
