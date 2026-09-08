<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AdminHelper;
class AdminController extends Controller
{
    public function getAdminDashboardOverview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|string|exists:admins,admin_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return AdminHelper::getAdminDashboardOverview($request);
    }
}
