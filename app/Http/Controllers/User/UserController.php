<?php

namespace App\Http\Controllers\User;

use App\Helper\_EmailHelper;
use App\Helper\_RuleHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Services\Interfaces\UserInterface;
use Exception;
use App\Models\User;
use App\Models\UserWorkshops;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;


class UserController extends Controller
{

    private UserInterface $user;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    
    public function exportCSV()
    {

        $csvFileName = 'users-' . strtotime(date('Y-m-d H:i:s')) . '.csv';
        $headers = [
            "Content-Encoding" => "UTF-8",
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
        ];
        $data = User::query()->get();
        $callback = function () use ($data) {
            $columns = ['Name', 'Email', 'Member Type', 'Phone', 'Job Title', 'department', 'hospital', 'Bayanati Number', 'Speciality', 'Code', 'Created At'];
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            foreach ($data as $key => $item) {
                $row = [
                    $item->name,
                    $item->email,
                    $item->member_type,
                    $item->phone,
                    $item->jobTitle,
                    $item->department,
                    $item->hospital,
                    $item->bayanati_number,
                    $item->speciality,
                    $item->code()->first() ? $item->code()->first()->code : "-",
                    $item->created_at,
                ];
                // Log::error(json_encode($row, true));
                $row_utf8 = array_map('utf8_encode', $row);

                fputcsv($file, $row_utf8);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportWorkshopCSV()
    {

        $csvFileName = 'users-workshop-' . strtotime(date('Y-m-d H:i:s')) . '.csv';
        $headers = [
            "Content-Encoding" => "UTF-8",
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$csvFileName",
        ];
        $data = UserWorkshops::query()->get();
        $callback = function () use ($data) {
            $columns = ['Name','Email','Member Type','Job Title','Department','Hospital','Bayanati Number','Speciality','Code', 'option_1', 'option_2', 'Created At'];
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            foreach ($data as $key => $item) {
                $user = $item->User()->first();
                if($user){
                $row = [
                    $user ? $user->name:"-",
                    $user ? $user->email:"-",
                    $user ? $user->member_type:"-",
                    $user ? $user->jobTitle:"-",
                    $user ? $user->department:"-",
                    $user ? $user->hospital:"-",
                    $user ? $user->bayanati_number:"-",
                    $user ? $user->speciality:"-",
                    $user ? ($user->code()->first() ? $user->code()->first()->code : "-"):"-",
                    $item->option_1,
                    $item->option_2,
                    $item->created_at,
                ];
                $row_utf8 = array_map('utf8_encode', $row);
                fputcsv($file, $row_utf8);
            }

            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }


    public function profile(Request $request): UserResource|JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            if ($user) {
                return UserResource::create($user);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return UserResource|JsonResponse
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $user = $this->user->store($request);

    //         if ($user) {
    //             if($user->member_type=="EHS Staff"){
    //                 _EmailHelper::sendEmail($user, [], 'thanks', "Thank you for registering");
    //             }else{
    //                 _EmailHelper::sendEmail($user, [], 'thanks_external', "Thank you for registering");
    //             }
    //             return UserResource::create($user);
    //         }
    //         return BaseResource::return();
    //     } catch (Exception $exception) {
    //         Log::error($exception);
    //         return BaseResource::return($exception->getMessage());
    //     }
    // }




    public function store(Request $request)
{
    try {

        $paymentIntent = null;

        $isPaidUser = $request->member_type === "Non EHS Attendee" && empty($request->code);

        // 🔐 VERIFY STRIPE PAYMENT
        if ($isPaidUser) {

            if (!$request->payment_intent_id) {
                return BaseResource::return(
                    "Payment is required to complete registration",
                    false,
                    400
                );
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            // ❗ ensure payment succeeded
            if (!$paymentIntent || $paymentIntent->status !== 'succeeded') {
                return BaseResource::return(
                    "Payment not verified",
                    false,
                    400
                );
            }

            // ❗ prevent reuse
            if (\DB::table('payments')
                ->where('payment_intent_id', $paymentIntent->id)
                ->exists()) {

                return BaseResource::return(
                    "Payment already used",
                    false,
                    400
                );
            }
        }

        // ✅ CREATE USER
        $user = $this->user->store($request);

        if (!$user) {
            return BaseResource::return("User creation failed", false, 500);
        }

        // ✅ SAVE PAYMENT
        if ($isPaidUser) {

            \DB::table('payments')->insert([
    'user_id' => $user->id,
    'payment_intent_id' => $paymentIntent->id,
    'amount' => $paymentIntent->amount,
    'currency' => strtoupper($paymentIntent->currency),
    'status' => $paymentIntent->status,
    'created_at' => now(),
    'updated_at' => now(),
]);
        }

        // ✅ SEND EMAIL
        $emailSent = _EmailHelper::sendEmail(
            $user,
            [],
            'thanks',
            "Thank you for registering"
        );

        if (!$emailSent) {
            Log::error('Email failed for user ID: ' . $user->id);
        }

        return UserResource::create($user);

    } catch (Exception $exception) {

        Log::error($exception);

        return BaseResource::return($exception->getMessage());

    }
}

//     public function store(Request $request)
// {
//     try {
//         $paymentIntent = null;

//         // 🔐 VERIFY STRIPE PAYMENT (only for paid users)
//         if ($request->member_type === "Non EHS Attendee" && $request->code ==="") {

//             if (!$request->payment_intent_id) {
//                 return BaseResource::return(
//                     "Payment is required to complete registration",
//                     false,
//                     400
//                 );
//             }

//             Stripe::setApiKey(config('services.stripe.secret'));

//             $paymentIntent = PaymentIntent::retrieve(
//                 $request->payment_intent_id
//             );

//             if ($paymentIntent->status !== 'succeeded') {
//                 return BaseResource::return(
//                     "Payment not verified",
//                     false,
//                     400
//                 );
//             }

//             // OPTIONAL: prevent reuse of same payment
//             if (\DB::table('payments')
//                 ->where('payment_intent_id', $paymentIntent->id)
//                 ->exists()) {

//                 return BaseResource::return(
//                     "Payment already used",
//                     false,
//                     400
//                 );
//             }
//         }

//         // ✅ CREATE USER
//         $user = $this->user->store($request);

//         if ($user) {

//             // ✅ SAVE PAYMENT (RECOMMENDED)
//             if ($request->member_type === "Non EHS Attendee" && $request->code ==="") {
//                 \DB::table('payments')->insert([
//                     'user_id' => $user->id,
//                     'payment_intent_id' => $paymentIntent->id,
//                     'amount' => $paymentIntent->amount,
//                     'currency' => $paymentIntent->currency,
//                     'status' => $paymentIntent->status,
//                     'created_at' => now(),
//                 ]);
//             }

//             // ✅ SEND EMAIL
//             if ($user->member_type === "EHS Staff") {
//                 $emailSent = _EmailHelper::sendEmail(
//     $user,
//     [],
//     'thanks',
//     "Thank you for registering"
// );

// if (!$emailSent) {
//     Log::error('Email failed for user ID: ' . $user->id);
// }
//             } else {
//                 _EmailHelper::sendEmail(
//                     $user,
//                     [],
//                     'thanks',
//                     "Thank you for registering"
//                 );
//             }

//             return UserResource::create($user);
//         }

//         return BaseResource::return();

//     } catch (Exception $exception) {
//         Log::error($exception);
//         return BaseResource::return($exception->getMessage());
//     }
// }

    public function contact(Request $request)
    {
        try {
            $rules = [
                "name" => _RuleHelper::_Rule_Require,
                "email" => _RuleHelper::_Rule_Require . "|" . _RuleHelper::_Rule_Email,
                'message_content' => _RuleHelper::_Rule_Require,
            ];
            $request->validate($rules);
            $res = _EmailHelper::sendEmailToInfo($request->input('email'), [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'message' => $request->input('message_content'),
            ], 'contact', 'Contact Us');
            Log::error($res);
            if ($res) {
                return BaseResource::ok();
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $user = $this->user->loginByEmail($request);
            if ($user) {
                return UserResource::create($user);
            }
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage());
        }
    }

    public function getAllUsers(): JsonResponse
    {
    try {
        // Fetch all users
        $users = User::all(); // Retrieves all records from the `users` table

        // Check if users are present
        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No users found',
                'data' => []
            ], 404);
        }

        // Return users as JSON response
        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);

    } catch (Exception $exception) {
        // Handle any exceptions
        Log::error('Error retrieving users: ' . $exception->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving users',
            'error' => $exception->getMessage()
        ], 500);
    }}
    

public function bulkStore(Request $request)
    {
        try {
            $data = $request->validate([
                'users' => ['required', 'array', 'min:1'],

                'users.*.email'          => ['required', 'email'],
                'users.*.name'           => ['nullable', 'string', 'max:255'],
                'users.*.phone'          => ['nullable', 'string', 'max:50'],
                'users.*.password'       => ['nullable', 'string', 'min:6'],
                'users.*.hospital'       => ['nullable', 'string', 'max:255'],
                'users.*.bayanati_number'=> ['nullable', 'string', 'max:255'],
                'users.*.speciality'     => ['nullable', 'string', 'max:255'],
                'users.*.profession'     => ['nullable', 'string', 'max:255'],
                'users.*.title'          => ['nullable', 'string', 'max:255'],
                'users.*.job_title'      => ['nullable', 'string', 'max:255'],
                'users.*.work_place'     => ['nullable', 'string', 'max:255'],
                'users.*.is_ehs'         => ['nullable', 'boolean'],
            ]);

            $created = [];
            $updated = [];

            DB::beginTransaction();

            foreach ($data['users'] as $userData) {
                // Prepare fields allowed to be saved
                $payload = [
                    'name'            => $userData['name']           ?? null,
                    'phone'           => $userData['phone']          ?? null,
                    'hospital'        => $userData['hospital']       ?? null,
                    'bayanati_number' => $userData['bayanati_number']?? null,
                    'speciality'      => $userData['speciality']     ?? null,
                    'profession'      => $userData['profession']     ?? null,
                    'title'           => $userData['title']          ?? null,
                    'job_title'       => $userData['job_title']      ?? null,
                    'work_place'      => $userData['work_place']    ?? null,
                ];

                if (array_key_exists('is_ehs', $userData)) {
                    $payload['is_ehs'] = (bool)$userData['is_ehs'];
                }

                // Only set password if provided
                if (!empty($userData['password'])) {
                    $payload['password'] = bcrypt($userData['password']);
                }

                $user = User::updateOrCreate(
                    ['email' => $userData['email']], // unique key
                    $payload
                );

                if ($user->wasRecentlyCreated) {
                    $created[] = $user;
                } else {
                    $updated[] = $user;
                }

                // Send email (example: internal vs external)
                if ($user->is_ehs) {
                    _EmailHelper::sendEmail($user, [], 'thanks', "Thank you for registering");
                } else {
                    _EmailHelper::sendEmail($user, [], 'thanks_external', "Thank you for registering");
                }
            }

            DB::commit();

            return BaseResource::return(null, [
                'created_count' => count($created),
                'updated_count' => count($updated),
                'created'       => UserResource::collection(collect($created)),
                'updated'       => UserResource::collection(collect($updated)),
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return BaseResource::return($exception->getMessage());
        }
    }


}
