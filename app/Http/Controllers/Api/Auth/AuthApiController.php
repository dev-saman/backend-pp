<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        try {

            Log::channel('auth')->info('Login API hit', [
                'email' => $request->email
            ]);

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            Log::channel('auth')->info('Attempting login', [
                'email' => $credentials['email']
            ]);

            if (!$token = Auth::guard('api')->attempt($credentials)) {
                Log::channel('auth')->warning('Invalid credentials', [
                    'email' => $credentials['email']
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            // ✅ Get logged-in user
            $user = Auth::guard('api')->user();
            Log::channel('auth')->info('Login successful', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            Log::channel('auth')->error('Login failed', [
                'email' => $request->email ?? null,
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            Log::channel('auth')->info('Logout API hit', [
                'user_id' => $user->id ?? null
            ]);
            // invalidate current token
            Auth::guard('api')->logout();
            Log::channel('auth')->info('Logout successful', [
                'user_id' => $user->id ?? null
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            Log::channel('auth')->error('Logout failed', [
                'user_id' => Auth::guard('api')->id(),
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout'
            ], 500);
        }
    }
}
