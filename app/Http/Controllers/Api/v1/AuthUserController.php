<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    public function register(RegisterRequest $request) {
        
        try{
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;

            $user->password = Hash::make($request->password, [
                'rounds' => 12
            ]);
            
            $user->save();

            return response()->json([
                'code' => 200,
                'message' => "Registered successfully",
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
        
    }

    public function login (LogUserRequest $request) {
        if(Auth::attempt($request->only(['email', 'password']))) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'code' => 200,
                'message' => "Login Successfully",
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "Wrong username or password.",
            ], 403);
        }
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
