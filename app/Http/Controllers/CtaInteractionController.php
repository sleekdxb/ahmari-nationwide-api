<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Helpers\CtaInteractionHelper;

class CtaInteractionController extends Controller
{
    //


    public function setInteraction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'veh_id' => 'required|string|exists:vehicles,veh_id',
            'cta_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return CtaInteractionHelper::setInteraction($request);
    }
}
