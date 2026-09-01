<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\Admin;
use App\Models\AdminSession;
use App\Models\AdminStatus;
use Log;


class AdminAuthHelper
{

    public static function addStaff(Request $request)
    {
        try {
            //  Generate unique admin_id
            $adminId = 'ADMIN_' . strtoupper(Str::random(15));

            //  Hash the email for storage
            $hashedEmail = hash('sha256', strtolower($request->input('email')));

            //  Create admin
            $admin = Admin::create([
                'admin_id' => $adminId,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'hashed_email' => $hashedEmail,            // store hashed email
                'password' => Hash::make($request->input('password')),
                // 'role' => $request->input('role'),
            ]);

            //  Generate unique state_id
            $stateId = 'STATE_' . strtoupper(Str::random(12));

            //  Create initial admin account status
            $status = AdminStatus::create([
                'admin_id' => $admin->admin_id,
                'state_id' => $stateId,
                'state' => 'Active',
                'note' => 'Admin account created with active Status'
            ]);

            //  Update admin with state_id
            $admin->state_id = $status->state_id;
            $admin->save();

            return response()->json([
                'success' => true,
                'message' => 'Admin added successfully',
                'data' => $admin
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function login(Request $request)
    {
        try {
            //  Hash email
            $hashedEmail = hash('sha256', strtolower($request->input('email')));

            //  Find admin
            $admin = Admin::with('currentState')->where('hashed_email', $hashedEmail)->first();

            if (!$admin || !Hash::check($request->input('password'), $admin->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            //  Generate JWT (SAFE way)
            $token = JWTAuth::fromUser($admin);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token generation failed'
                ], 500);
            }

            //  Create session
            $session = AdminSession::create([
                'session_id' => 'SESSION_' . strtoupper(Str::random(20)),
                'admin_id' => $admin->admin_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'payload' => json_encode([
                    'access_token' => $token,
                    'login_at' => now()->toDateTimeString(),
                ]),
                'expires_at' => now()->addWeek(),
            ]);



            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'admin' => [
                        'admin_id' => $admin->admin_id,
                        'name' => $admin->name,
                        'email' => $admin->email,
                        'state_id' => $admin->state_id,
                    ],
                    'currentState' => $admin->currentState,
                    'access_token' => $token,
                    'session_id' => $session->session_id
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public static function logout(Request $request)
    {
        try {
            $session = AdminSession::where(
                'session_id',
                $request->input('session_id')
            )->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found',
                ], 404);
            }

            // Get payload
            $payload = json_decode($session->payload, true);

            // Get JWT token from session
            $token = $payload['access_token'] ?? null;

            // Invalidate JWT
            if ($token) {
                try {
                    JWTAuth::setToken($token)->invalidate();
                } catch (\Exception $e) {
                    // Token may already be expired/invalid.
                    // Continue deleting the session.
                }
            }

            // Delete session
            $session->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}