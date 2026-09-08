<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Vehicle;
use App\Models\CtaInteraction;
use App\Models\Inquiry;

class AdminHelper
{

    public static function getAdminDashboardOverview(Request $request): JsonResponse
    {
        $vehicles = Vehicle::with(['currentState', 'engagements'])->get();

        $engagements = CtaInteraction::all();

        $contactRequests = Inquiry::all();

        /*
        |--------------------------------------------------------------------------
        | Fig Cards
        |--------------------------------------------------------------------------
        */

        $totalActiveListings = $vehicles->count();

        $totalAvailable = $vehicles->filter(function ($vehicle) {
            return strtolower($vehicle->currentState?->name ?? '') === 'available';
        })->count();

        $totalPending = $vehicles->filter(function ($vehicle) {
            return strtolower($vehicle->currentState?->name ?? '') === 'pending';
        })->count();

        $totalSold = $vehicles->filter(function ($vehicle) {
            return strtolower($vehicle->currentState?->name ?? '') === 'sold';
        })->count();


        /*
        |--------------------------------------------------------------------------
        | CTA Engagements
        |--------------------------------------------------------------------------
        | Dynamically count CTA types from the database.
        |--------------------------------------------------------------------------
        */

        $ctaEngagements = $engagements
            ->groupBy(function ($engagement) {
                return strtolower(trim($engagement->cta_type ?? 'unknown'));
            })
            ->map(function ($items) {
                return $items->count();
            });


        /*
        |--------------------------------------------------------------------------
        | Traffic Sources
        |--------------------------------------------------------------------------
        | Calculate percentage for every source.
        |--------------------------------------------------------------------------
        */

        $totalSourceEngagements = $engagements->count();

        $trafficSources = $engagements
            ->groupBy(function ($engagement) {
                return $engagement->source ?: 'unknown';
            })
            ->map(function ($items) use ($totalSourceEngagements) {
                $count = $items->count();

                return [
                    'count' => $count,
                    'percentage' => $totalSourceEngagements > 0
                        ? round(($count / $totalSourceEngagements) * 100, 2)
                        : 0,
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | Contact Requests
        |--------------------------------------------------------------------------
        */

        $contactRequestData = $contactRequests->map(function ($request) {
            return [
                'title' => trim(
                    ($request->name ?? '') .
                    ' ' .
                    ($request->email ?? '')
                ),

                'message' => $request->message ?? '',

                'time_ago' => $request->created_at
                    ? $request->created_at->diffForHumans()
                    : null,
            ];
        })->values();


        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        | Dummy data for now.
        |--------------------------------------------------------------------------
        */

        $activityLog = [
            [
                'title' => 'Vehicle Added - 2024 Toyota Camry',
                'description' => 'New vehicle listing was added.',
                'time_ago' => '7d',
            ],
            [
                'title' => 'Vehicle Updated - 2023 BMW X5',
                'description' => 'Vehicle listing was updated.',
                'time_ago' => '7d',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | Final Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'figCards' => [
                    'total_active_listings' => $totalActiveListings,
                    'total_available' => $totalAvailable,
                    'total_pending' => $totalPending,
                    'total_sold' => $totalSold,
                ],

                'cta_engagements' => $ctaEngagements,

                'traffic_sources' => $trafficSources,

                'contact_requests' => $contactRequestData,

                'activity_log' => $activityLog,
            ],
        ], 200);
    }


}