<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $valid = Helper::validator($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($valid == true) {
            if ($token = Auth::attempt($credentials)) {

                // if (Auth::user()->hasRole(['Super Admin', 'Security'])) {
                    // $user = Auth::user();
                    $user = User::with(['roles' => function($query){
                        $query->with(['permissions' => function($query) {
                            $query->select('id', 'name');
                        }])->select('id', 'name');
                    }])->where('id', auth()->user()->id)->first();
                    // $user->getRoleNames();
                    // $user->getAllPermissions();

                    return response()->json([
                        'success' => true,
                        'message' => "Login Success",
                        'token' => $token,
                        'token_type' => 'bearer',
                        'user' => $user
                    ]);
                // } else {
                //     return response()->json([
                //         'success' => false,
                //         'message' => "You Don't Have Access",
                //     ], 401);
                // }
            }

            return response()->json([
                'success' => false,
                'message' => "Login Failed",
            ], 422);
        }
    }

    public function me()
    {
        $user = Auth::user();
        $user->getRoleNames();
        $user->getAllPermissions();

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function change_password(Request $request)
    {
        $valid = Helper::validator($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        if ($valid === true) {
            try {
                $user = Auth::user();
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => "User tidak di temukan",
                    ], 404);
                }

                $checkPassword = Hash::check($request->input('old_password'), $user->password);
                if (!$checkPassword) {
                    return response()->json([
                        'success' => false,
                        'message' => "Password Lama Salah",
                    ], 422);
                }

                $user->password = Hash::make($request->input('new_password'));
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => "Success Change Password",
                    'data' => $user
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Failed Change Password",
        ], 422);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Logout Success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'succes' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
