<?php

namespace App\Helpers;

use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InquiryHelper
{
    public static function addInquiry(Request $request): JsonResponse
    {
        try {
            // Generate unique inquiry ID
            $inqId = 'INQ-' . strtoupper(Str::random(12));

            // Create inquiry
            $inquiry = Inquiry::create([
                'client_id' => $request->client_id ?? null,
                'inq_id' => $inqId,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inquiry submitted successfully.',
                'data' => $inquiry,
            ], 201);

        } catch (\Throwable $e) {

            Log::error('Failed to add inquiry', [
                'name' => $request->name,
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit inquiry.',
            ], 500);
        }
    }
}

