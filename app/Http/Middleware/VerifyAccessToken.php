<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class VerifyAccessToken
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        try {

            
            /*
             * Get Bearer token
             */
            $token = $request->bearerToken();

            
            if (!$token) {



                return response()->json([
                    'success' => false,
                    'message' => 'Access token is required.',
                ], 401);
            }

            /*
             * Set JWT token
             */
            JWTAuth::setToken($token);

            /*
             * Validate and decode JWT.
             *
             * If the token is expired, malformed, or has an
             * invalid signature, this will throw JWTException.
             */
            $payload = JWTAuth::getPayload();

            $subject = $payload->get('sub');


            /*
             * Authenticate using the API JWT guard.
             */
            $user = auth('api')->user();

            /*
             * IMPORTANT:
             *
             * Do not use:
             *
             * $user?->getAuthIdentifier()
             *
             * because JWTAuth can return false.
             */
      

            /*
             * Authentication failed
             */
            if (!is_object($user)) {

                Log::warning(
                    'VerifyAccessToken: User could not be resolved',
                    [
                        'subject' => $subject,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid access token.',
                ], 401);
            }

            /*
             * Authentication successful
             */
           

            /*
             * Make the authenticated Admin available through
             * $request->user()
             */
            $request->setUserResolver(
                fn () => $user
            );

            return $next($request);

        } catch (JWTException $e) {

            Log::warning(
                'VerifyAccessToken: JWT exception',
                [
                    'exception_class' => get_class($e),
                    'message' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired access token.',
            ], 401);

        } catch (\Throwable $e) {

            Log::error(
                'VerifyAccessToken: Unexpected exception',
                [
                    'exception_class' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Authentication failed.',
            ], 500);
        }
    }
}
