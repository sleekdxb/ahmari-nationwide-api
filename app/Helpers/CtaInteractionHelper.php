<?php

namespace App\Helpers;



use App\Models\CtaInteraction;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;




class CtaInteractionHelper
{
    public static function setInteraction(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'veh_id' => ['required', 'string'],
                'cta_type' => ['required', 'string'],
            ]);

            $vehicle = Vehicle::where('veh_id', $validated['veh_id'])->first();

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found.',
                ], 404);
            }

            $ctaId = 'CTA-' . strtoupper(Str::random(12));

            $interaction = CtaInteraction::create([
                'cta_id' => $ctaId,
                'veh_id' => $vehicle->veh_id,
                'cta_type' => $validated['cta_type'],
                'acted_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'CTA interaction recorded successfully.',
                'data' => $interaction,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Failed to set CTA interaction', [
                'veh_id' => $request->veh_id,
                'cta_type' => $request->cta_type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record CTA interaction.',
            ], 500);
        }
    }

}