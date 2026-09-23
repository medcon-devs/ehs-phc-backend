<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Resources\BaseResource;
use Exception;
class JotformWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log full wrapper payload (what you already saw)
        Log::info('JOTFORM WEBHOOK PAYLOAD', $request->all());

        $wrapper = $request->all();

        // 🔹 Decode the rawRequest JSON from Jotform
        $rawJson = $wrapper['rawRequest'] ?? '{}';
        $payload = json_decode($rawJson, true) ?? [];

        Log::info('JOTFORM DECODED PAYLOAD', $payload);

        // 🔹 Map fields from decoded payload
        $profession = $payload['q8_profession']          ?? null;
        $speciality = $payload['q20_speciality']         ?? null;
        $title      = $payload['q9_title']               ?? null;
        $fullName   = $payload['q10_fullName']           ?? null;
        $email      = $payload['q15_email15']            ?? null; // note: q15_email15 from log
        $jobTitle   = $payload['q11_jobTitle']           ?? null;
        $workPlace  = $payload['q12_workPlace']          ?? null;
        $phone      = $payload['q13_phoneNumber']        ?? null;
        $bayanati   = $payload['q16_bayanatiNumber']     ?? null;
        $regCat     = $payload['q4_registrationCategory'] ?? null; // "EHS" or something else

        // 🔹 is_ehs logic: based on Registration Category
        $isEhs = ($regCat === 'EHS');

        // Optional safety: if email is empty, log & stop
        if (empty($email)) {
            Log::warning('JOTFORM: email is empty, user not created', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Email is required but missing in payload',
            ], 422);
        }

        // 🔹 Create or update user by email
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'            => $fullName,
                'phone'           => $phone,
                'hospital'        => $workPlace,       // Work Place → hospital
                'bayanati_number' => $bayanati,
                'speciality'      => $speciality,
                'profession'      => $profession,
                'title'           => $title,
                'job_title'       => $jobTitle,
                'work_place'      => $workPlace,
                'is_ehs'          => $isEhs,
                // 'code_id'         => 0,
                // 'code_id'      => ... (set this if you have a code logic)
            ]
        );

        return response()->json([
            'status'  => 'ok',
            'id'      => $user->id,
            'message' => 'Jotform submission stored successfully',
        ]);
    }



    public function bulkStore(Request $request)
{
    try {
        // Log the incoming payload for debugging
        Log::info('BULK USER IMPORT PAYLOAD', $request->all());

        $data = $request;
        // ->validate([
        //     'users' => ['required', 'array', 'min:1'],
        //     'users.*.profession' => ['nullable', 'string', 'max:255'],
        //     'users.*.speciality' => ['nullable', 'string', 'max:255'],
        //     'users.*.title' => ['nullable', 'string', 'max:255'],
        //     'users.*.fullName' => ['nullable', 'string', 'max:255'],
        //     'users.*.email' => ['required', 'email'],
        //     'users.*.jobTitle' => ['nullable', 'string', 'max:255'],
        //     'users.*.workPlace' => ['nullable', 'string', 'max:255'],
        //     'users.*.phone' => ['nullable', 'string', 'max:50'],
        //     'users.*.bayanati' => ['nullable', 'string', 'max:255'],
        //     'users.*.regCat' => ['nullable', 'string', 'max:255'],
        //     'users.*.password' => ['nullable', 'string', 'min:6'], // Optional password
        // ]);

        $created = [];
        $updated = [];
        $errors = [];

        DB::beginTransaction();

        foreach ($data['users'] as $index => $userData) {
            try {
                // Validate required email
                if (empty($userData['email'])) {
                    $errors[] = "User at index $index: Email is required";
                    continue;
                }

                // Map fields exactly like your Jotform handler
                $profession = $userData['profession'] ?? null;
                $speciality = $userData['speciality'] ?? null;
                $title = $userData['title'] ?? null;
                $fullName = $userData['fullName'] ?? null;
                $email = $userData['email'];
                $jobTitle = $userData['jobTitle'] ?? null;
                $workPlace = $userData['workPlace'] ?? null;
                $phone = $userData['phone'] ?? null;
                $bayanati = $userData['bayanati'] ?? null;
                $regCat = $userData['regCat'] ?? null;

                // 🔹 is_ehs logic: based on Registration Category (same as your Jotform logic)
                $isEhs = ($regCat === 'EHS');

                // Prepare user data
                $userPayload = [
                    'name' => $fullName,
                    'phone' => $phone,
                    'hospital' => $workPlace,       // Work Place → hospital (same mapping)
                    'bayanati_number' => $bayanati,
                    'speciality' => $speciality,
                    'profession' => $profession,
                    'title' => $title,
                    'job_title' => $jobTitle,
                    'work_place' => $workPlace,
                    'is_ehs' => $isEhs,
                    // Set member_type based on is_ehs if you need it
                    'member_type' => $isEhs ? 'EHS Staff' : 'External',
                ];

                // Only set password if provided
                if (!empty($userData['password'])) {
                    $userPayload['password'] = bcrypt($userData['password']);
                }

                // Create or update user by email (same logic as your Jotform handler)
                $user = User::updateOrCreate(
                    ['email' => $email],
                    $userPayload
                );

                if ($user->wasRecentlyCreated) {
                    $created[] = $user;
                    
                    // Send welcome email for new users only (same logic as your store method)
                    if ($user->is_ehs) {
                        // _EmailHelper::sendEmail($user, [], 'thanks', "Thank you for registering");
                    } else {
                        // _EmailHelper::sendEmail($user, [], 'thanks_external', "Thank you for registering");
                    }
                } else {
                    $updated[] = $user;
                }

            } catch (Exception $userException) {
                $errors[] = "User {$userData['email']}: " . $userException->getMessage();
                Log::error("Failed to process user {$userData['email']}: " . $userException->getMessage());
                continue;
            }
        }

        DB::commit();

       return response()->json([
    'success' => true,
    'message' => 'Bulk user opera tion completed',
    'data' => [
        'created_count' => count($created),
        'updated_count' => count($updated),
        'error_count' => count($errors),
        'created' => UserResource::collection(collect($created)),
        'updated' => UserResource::collection(collect($updated)),
        'errors' => $errors,
    ]
], 200);

    } catch (Exception $exception) {
        DB::rollBack();
        Log::error('Bulk user import failed: ' . $exception->getMessage());

        return BaseResource::return($exception->getMessage());
    }
}


}
