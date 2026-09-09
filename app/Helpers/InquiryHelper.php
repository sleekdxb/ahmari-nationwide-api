<?php

namespace App\Helpers;

use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\InquiryStatus;

class InquiryHelper
{
    public static function addInquiry(Request $request): JsonResponse
    {
        try {
            // Generate unique inquiry ID
            $inqId = 'INQ-' . strtoupper(Str::random(12));

            // Generate unique state ID
            $stateId = 'STATE-' . strtoupper(Str::random(12));

            // Default inquiry state
            $stateName = 'NEW';

            // Create inquiry
            $inquiry = Inquiry::create([
                'client_id' => $request->client_id ?? null,
                'inq_id' => $inqId,
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'message' => $request->message,
                'state_id' => $stateId,
            ]);

            // Create inquiry status
            InquiryStatus::create([
                'inq_id' => $inqId,
                'state_id' => $stateId,
                'name' => strtoupper($stateName),
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


    public static function getInquiriesAdmin(Request $request): JsonResponse
    {
        try {
            $inquiries = Inquiry::with('currentState')->get();

            $totalNewRequests = $inquiries->filter(function ($inquiry) {
                return strtoupper($inquiry->currentState?->name ?? '') === 'NEW';
            })->count();

            $totalInProgressRequests = $inquiries->filter(function ($inquiry) {
                return strtoupper($inquiry->currentState?->name ?? '') === 'IN PROGRESS';
            })->count();

            $totalClosedRequests = $inquiries->filter(function ($inquiry) {
                return strtoupper($inquiry->currentState?->name ?? '') === 'CLOSED';
            })->count();

            $totalRequests = $inquiries->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'figCard' => [
                        'total_new_requests' => $totalNewRequests,
                        'total_in_progress_requests' => $totalInProgressRequests,
                        'total_closed_requests' => $totalClosedRequests,
                        'total_requests' => $totalRequests,
                    ],
                    'inquiries' => $inquiries,
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Failed to get admin inquiries', [
                'admin_id' => $request->admin_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiries.',
            ], 500);
        }
    }



    public static function setInquiryState(Request $request): JsonResponse
{
try {

    return DB::transaction(function () use ($request) {

        /*
         * 1. Find the inquiry using inq_id
         */
        $inquiry = Inquiry::where(
            'inq_id',
            $request->inq_id
        )->first();

        if (!$inquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Inquiry not found.'
            ], 404);
        }

        /*
         * 2. Generate unique state ID
         *
         * Example:
         * STATE-A1B2C3D4E5
         */
        $stateId = 'STATE-' . strtoupper(
            bin2hex(random_bytes(6))
        );

        /*
         * 3. Create inquiry status
         */
        $inquiryStatus = InquiryStatus::create([
            'inq_id'   => $request->inq_id,
            'state_id' => $stateId,
            'name'     => $request->name,
        ]);

        /*
         * 4. Update the current inquiry state_id
         */
        $inquiry->state_id = $stateId;
        $inquiry->save();

        /*
         * 5. Return successful response
         */
        return response()->json([
            'success' => true,
            'message' => 'Inquiry state added successfully.',

            'data' => [
                'inq_id'   => $inquiry->inq_id,
                'state_id' => $stateId,
                'name'     => $inquiryStatus->name,
            ]
        ], 201);
    });

} catch (\Exception $e) {

    return response()->json([
        'success' => false,
        'message' => 'Failed to add inquiry state.',
        'error'   => $e->getMessage()
    ], 500);
}

}
}

