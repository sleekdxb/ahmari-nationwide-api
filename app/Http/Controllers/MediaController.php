<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Helpers\MediaHelper;
class MediaController extends Controller
{
    public function vehicleFileUpload(Request $request): JsonResponse
    {
        // Step 1: Parse uploadProtocol if sent as JSON string
        if (is_string($request->input('uploadProtocol'))) {
            try {
                $uploadProtocol = json_decode(
                    $request->input('uploadProtocol'),
                    true
                );

                if (!is_array($uploadProtocol)) {
                    throw new \Exception('Invalid JSON structure.');
                }

                $request->merge([
                    'uploadProtocol' => $uploadProtocol
                ]);

            } catch (\Throwable $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid uploadProtocol JSON',
                    'errors' => [
                        'uploadProtocol' => [$e->getMessage()]
                    ],
                ], 422);
            }
        }

        // Step 2: Validate upload protocol
        $protocolValidator = Validator::make($request->all(), [
            'uploadProtocol' => 'required|array',

            'uploadProtocol.target' => [
                'required',
                'string',
                'in:vehicle,vehicle_update'
            ],

            'uploadProtocol.ref_id' => 'required|string',

            'uploadProtocol.upload_at' => 'required|date',

            'uploadProtocol.existing_file_ids' => [
                'required_if:uploadProtocol.target,vehicle_update',
                'array',
            ],

            'uploadProtocol.existing_file_ids.*' => [
                'required_if:uploadProtocol.target,vehicle_update',
                'string',
            ],
        ]);

        if ($protocolValidator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Upload protocol validation failed',
                'errors' => $protocolValidator->errors(),
            ], 422);
        }

        $target = $request->input('uploadProtocol.target');

        // Step 3: Validate vehicle files
        $fileRules = [
            'vehicle_img' => [
                'required_without:vehicle_doc',
                'array',
                'min:1'
            ],

            'vehicle_doc' => [
                'required_without:vehicle_img',
                'array',
                'min:1'
            ],

            'vehicle_img.*' => [
                'file',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:20480'
            ],

            'vehicle_doc.*' => [
                'file',
                'mimes:pdf,doc,docx',
                'max:20480'
            ],
        ];

        // For update, files are optional if only updating existing files
        if ($target === 'vehicle_update') {
            $fileRules['vehicle_img'][0] = 'sometimes';
            $fileRules['vehicle_doc'][0] = 'sometimes';
        }

        $fileValidator = Validator::make(
            $request->all(),
            $fileRules
        );

        if ($fileValidator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'File validation failed',
                'errors' => $fileValidator->errors(),
            ], 422);
        }

        // Step 4: Normalize uploaded files
        $allFiles = [];

        foreach (['vehicle_img', 'vehicle_doc'] as $field) {

            $files = $request->file($field);

            if (!$files) {
                continue;
            }

            $files = is_array($files)
                ? $files
                : [$files];

            foreach ($files as $file) {
                $allFiles[] = [
                    'field' => $field,
                    'file' => $file,
                ];
            }
        }

        // Step 5: Get upload protocol
        $uploadProtocol = $request->input('uploadProtocol');

        // Step 6: Upload/update vehicle files
        $response = MediaHelper::vehicleFileUpload(
            $uploadProtocol,
            $allFiles
        );

        return $response instanceof JsonResponse
            ? $response
            : response()->json([
                'status' => true,
                'message' => 'Vehicle files uploaded successfully',
                'data' => $response,
            ]);
    }

}
