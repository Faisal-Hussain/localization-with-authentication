<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class JWTAuthController
 *
 * Handles user authentication, including registration, login, logout,
 * and retrieving authenticated user details using JWT.
 */
class JWTAuthController extends Controller
{
    /**
     * Register a new user and return a JWT token.
     *
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        // Create the new user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return ApiResponse::success(['user' => $user, 'token' => $token], 'User registered successfully', 201);
    }

    /**
     * Authenticate user and return JWT token.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            // Attempt authentication
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return ApiResponse::error('Invalid credentials', 401);
            }

            return ApiResponse::success(['user' => auth()->user(), 'token' => $token], 'Login successful');
        } catch (JWTException $e) {
            return ApiResponse::error('Could not create token', 500);
        }
    }

    /**
     * Retrieve the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }
        } catch (JWTException $e) {
            return ApiResponse::error('Invalid token', 400);
        }

        return ApiResponse::success(['user' => $user]);
    }

    /**
     * Logout the user and invalidate the token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success([], 'Successfully logged out');
        } catch (JWTException $e) {
            return ApiResponse::error('Failed to logout', 500);
        }
    }
}
