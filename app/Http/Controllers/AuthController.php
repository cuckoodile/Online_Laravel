<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{

    /**
 * @OA\Post(
 *     path="/api/login",
 *     summary="User login",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username","password"},
 *             @OA\Property(property="username", type="string", example="johndoe"),
 *             @OA\Property(property="password", type="string", example="yourpassword")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="profile", type="object"),
 *             @OA\Property(property="roles", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */
    public function login(Request $request)
    {
        Log::info('Login attempt received', $request->only('username'));
    
        $validator = validator()->make($request->all(), [
            "username" => "required|string",
            "password" => "required|string"
        ]);
    
        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return $this->BadRequest($validator->errors());
        }
    
        // Check if the user exists
        $user = User::where('username', $request->username)->first();
    
        if (!$user) {
            Log::warning('User not found', ['username' => $request->username]);
            return $this->Unauthorized("Invalid credentials");
        }
    
        // Check password
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Password mismatch', ['username' => $request->username]);
            return $this->Unauthorized("Invalid credentials, password mismatch");
        }
    
        // Log the user in (optional for Laravel Sanctum — remove if using token auth only)
        try {
            auth('api')->login($user); // Note: Only works if JWT or session guard is configured
            Log::info('User successfully authenticated', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('Auth login failed', ['message' => $e->getMessage()]); 
        } // this is for debug only, you can modify it or remove it and change syntax for cleanliness
    
        // Load related data in array
        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'token' => $user->createToken("api")->plainTextToken,
            'profile' => $user->profile,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
    
        Log::info('Login successful, token generated', ['user_id' => $user->id]);
    
        return $this->Ok($userData, "Login successfully");
    }


    /**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="User logout",
 *     tags={"Auth"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        return $this->Ok($user, "Logout successful");
    }
     
    /**
 * @OA\Get(
 *     path="/api/user",
 *     summary="Get current authenticated user",
 *     tags={"Auth"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User has been retrieved"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */

    public function checkToken(Request $request)
    {
        $user = $request->user();
        $user->profile;
        $user->token = $request->bearerToken();
        return $this->Ok($user, "User has been retrieved");
    }   
}
