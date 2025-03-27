<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens; // Add this import

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = validator()->make($request->all(), [
            "username" => "required|string",
            "password" => "required|string"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        if (!auth()->attempt($validator->validated())) {
            return $this->Unauthorized("Invalid credentials");
        }

        $user = auth()->user();
        
        $user->profile;
        $user->token = $user->createToken("api")->plainTextToken;

        return $this->Ok($user, "Login successful");
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->Ok(null, "Logout successful");
    }

    public function checkToken(Request $request)
    {
        $user = $request->user();
        $user->profile;
        return $this->Ok($user, "User has been retrieved");
    }   
}
