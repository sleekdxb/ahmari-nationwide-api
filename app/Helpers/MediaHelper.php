<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleFile;
use App\Models\VehicleFileStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;


class MediaHelper
{
    public static function vehicleFileUpload(array $protocol, array $files): array
    {
        $target = $protocol['target'];
        $vehId = $protocol['ref_id'];
        $uploadedAt = Carbon::parse($protocol['upload_at'])->toDateTimeString();

        $now = now();

        // Verify vehicle exists
        $vehicle = Vehicle::where('veh_id', $vehId)->first();

        if (!$vehicle) {
            return [
                'status' => false,
                'message' => 'Vehicle not found.',
            ];
        }

        $disk = Storage::disk('hostinger');

        $uploadBaseUrl = rtrim(
            env('UPLOAD_BASE_URL', ''),
            '/'
        );

        $uploadEntries = [];
        $summary = [];
        $details = [];

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Process Files
            |--------------------------------------------------------------------------
            */

            foreach ($files as $item) {

                $field = $item['field'];

                $fileGroup = is_array($item['file'])
                    ? $item['file']
                    : [$item['file']];

                foreach ($fileGroup as $file) {

                    /*
                    |--------------------------------------------------------------------------
                    | Folder
                    |--------------------------------------------------------------------------
                    */

                    $timestamp = now()->format('YmdHis');

                    if ($field === 'vehicle_img') {
                        $baseDir = 'vehicle/Images';
                    } elseif ($field === 'vehicle_doc') {
                        $baseDir = 'vehicle/Documents';
                    } else {
                        $baseDir = 'vehicle/Misc';
                    }

                    $folderParts = [
                        $vehId,
                        "{$field}_{$timestamp}"
                    ];

                    $folder = $baseDir . '/' . implode('/', $folderParts);

                    if (!$disk->exists($folder)) {
                        $disk->makeDirectory(
                            $folder,
                            0755,
                            true
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Generate File ID
                    |--------------------------------------------------------------------------
                    */

                    $uuid = (string) Str::uuid();

                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    $filename = $uuid . '.' . $extension;

                    $path = $folder . '/' . $filename;

                    /*
                    |--------------------------------------------------------------------------
                    | Store File
                    |--------------------------------------------------------------------------
                    */

                    $disk->put(
                        $path,
                        fopen($file->getRealPath(), 'r')
                    );

                    $fullUrl = $uploadBaseUrl . '/' . $path;

                    /*
                    |--------------------------------------------------------------------------
                    | File Category
                    |--------------------------------------------------------------------------
                    */

                    $fileCategory = match ($field) {
                        'vehicle_img' => 'Vehicle Image',
                        'vehicle_doc' => 'Vehicle Document',
                        default => 'Vehicle Miscellaneous',
                    };

                    /*
                    |--------------------------------------------------------------------------
                    | Prepare DB Entry
                    |--------------------------------------------------------------------------
                    */

                    $uploadEntries[] = [
                        'file_id' => $uuid,
                        'veh_id' => $vehId,
                        'file_name' => $originalName,
                        'file_path' => $path,
                        'file_url' => $fullUrl,
                        'file_type' => $file->getMimeType(),

                        // state_id will be added after creating status
                        'state_id' => null,

                        'file_category' => $fileCategory,
                        'uploaded_at' => $uploadedAt,
                    ];

                    $summary[$field] =
                        ($summary[$field] ?? 0) + 1;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Save Vehicle Files + Status
            |--------------------------------------------------------------------------
            */

            foreach ($uploadEntries as $entry) {

                /*
                |--------------------------------------------------------------------------
                | Generate STATE ID
                |--------------------------------------------------------------------------
                |
                | Example:
                | STATEa8f3c9e12b7d4...
                |
                */

                $stateId = 'STATE' . hash(
                    'sha256',
                    $entry['file_id'] . $vehId . Str::uuid()
                );

                /*
                |--------------------------------------------------------------------------
                | Create Vehicle File
                |--------------------------------------------------------------------------
                */

                $vehicleFile = VehicleFile::create([
                    'file_id' => $entry['file_id'],
                    'veh_id' => $entry['veh_id'],
                    'file_name' => $entry['file_name'],
                    'file_path' => $entry['file_path'],
                    'file_url' => $entry['file_url'],
                    'file_type' => $entry['file_type'],
                    'state_id' => $stateId,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Create Vehicle File Status
                |--------------------------------------------------------------------------
                */

                $vehicleFileStatus = VehicleFileStatus::create([
                    'file_id' => $entry['file_id'],
                    'state_id' => $stateId,
                    'name' => 'uploaded',
                ]);

                $details[] = [
                    'upload_id' => $vehicleFile->id,
                    'file_id' => $entry['file_id'],
                    'veh_id' => $vehId,
                    'file_name' => $entry['file_name'],
                    'file_path' => $entry['file_path'],
                    'file_url' => $entry['file_url'],
                    'file_type' => $entry['file_type'],
                    'file_category' => $entry['file_category'],
                    'state_id' => $stateId,
                    'state' => $vehicleFileStatus->name,
                    'uploaded_at' => $entry['uploaded_at'],
                ];
            }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Vehicle files uploaded successfully.',
                'veh_id' => $vehId,
                'upload_base_url' => $uploadBaseUrl,
                'summary' => $summary,
                'uploaded' => $details,
            ];

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Vehicle file upload failed', [
                'veh_id' => $vehId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Failed to upload vehicle files.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
