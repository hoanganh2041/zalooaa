<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Constants\ResCode;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'error_code' => ResCode::UNAUTHENTICATED,
                    'message' => ResCode::UNAUTHENTICATED_MSG
                ]);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Error in Login');
            }
           
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $auth_token = explode('|', $tokenResult)[1];
            
            return response()->json([
                'error_code' => ResCode::SUCCESS,
                'access_token' => $auth_token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $error) {
            return response()->json([
                'error_code' => ResCode::UNAUTHENTICATED,
                'message' => ResCode::UNAUTHENTICATED_MSG
            ]);
        }
    }

    public function getUserInfo()
    {
        return response()->json([
            'data' => \auth()->user(),
        ]);
    }

    public function logoutCurrent()
    {
       // Revoke the user's current token...
       Auth::user()->currentAccessToken()->delete();    
        return response()->json([
            'error_code' => ResCode::SUCCESS,
            'message' => ResCode::SUCCESS_MSG,
        ]);
    }

    public function logoutAll()
    {
       // Revoke all tokens...
        Auth::user()->tokens()->delete(); 
        return response()->json([
            'error_code' => ResCode::SUCCESS,
            'message' => ResCode::SUCCESS_MSG,
        ]);
    }
}
