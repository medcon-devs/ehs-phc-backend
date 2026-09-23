<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class AttendeeController extends Controller
{
    /**
     * Display a listing of attendees.
     */
    public function index()
    {
        $attendees = Attendee::with('user')->latest()->get();
        return response()->json($attendees);
    }

    public function attend(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'type' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation Error',
            'status_code' => 422,
            'data' => $validator->errors()
        ], 422);
    }

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found',
            'status_code' => 404,
            'data' => null
        ], 404);
    }

    // Check for existing attendance within the last 5 minutes
    $latestAttendance = Attendee::where('user_id', $user->id)
        ->latest('created_at')
        ->first();

    if ($latestAttendance) {
        $createdAt = Carbon::parse($latestAttendance->created_at);
        $now = Carbon::now();

        if ($now->diffInMinutes($createdAt) <= 5) {
            return response()->json([
                'status' => false,
                'message' => 'User has already attended within the last 5 minutes.',
                'status_code' => 202,
                'data' => null
            ], 202);
        }
    }

    // Record new attendance
    $attendee = Attendee::create([
        'user_id' => $user->id,
        'type' => $request->type,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Attendance recorded successfully.',
        'status_code' => 200,
        'data' => [
            'id' => $attendee->id,
            'user_id' => $attendee->user_id,
            'type' => $attendee->type,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'created_at' => $attendee->created_at,
        ]
    ], 200);
}
    /**
     * Store a newly created attendee in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $attendee = Attendee::create([
            'user_id' => $request->user_id,
        ]);

        return response()->json($attendee, 201);
    }

    /**
     * Display the specified attendee.
     */
    public function show(Attendee $attendee)
    {
        return response()->json($attendee);
    }

    /**
     * Remove the specified attendee from storage.
     */
    public function destroy(Attendee $attendee)
    {
        $attendee->delete();
        return response()->json(['message' => 'Attendee deleted successfully']);
    }

   public function getAttendees()
{
    // Conference dates
    $conferenceDays = [
        'friday' => '2026-09-11',
        'saturday' => '2026-09-12',
        'sunday' => '2026-09-13',
    ];

    // Fetch attendance records with user details
    $attendees = DB::table('attendees')
        ->join('users', 'attendees.user_id', '=', 'users.id')
        ->select(
            'attendees.id as attendee_id',
            'users.id as user_id',
            'users.name as full_name',
            'users.email',
            'users.member_type',
            'users.phone',
            'users.jobTitle',
            'users.department',
            'users.hospital',
            'users.bayanati_number',
            'users.speciality',
            'users.code_id',
            'users.user_status',
            'attendees.created_at'
        )
        ->whereDate('attendees.created_at', '>=', '2026-09-11')
        ->whereDate('attendees.created_at', '<=', '2026-09-13')
        ->orderBy('attendees.created_at', 'asc')
        ->get();

    // Build response for each conference day
    $formattedData = collect();

    foreach ($conferenceDays as $dayName => $date) {

        // Get attendance records for this day
        $dayRecords = $attendees->filter(function ($item) use ($date) {
            return Carbon::parse($item->created_at)->format('Y-m-d') === $date;
        });

        // Group multiple scans/check-ins by user
        $groupedAttendees = $dayRecords->groupBy('user_id');

        // Format unique attendees for the day
        $formattedAttendees = $groupedAttendees
            ->map(function ($items) {

                $firstItem = $items->first();

                return [
                    'attendee_id' => $firstItem->attendee_id,
                    'user_id' => $firstItem->user_id,
                    'full_name' => $firstItem->full_name,
                    'email' => $firstItem->email,
                    'member_type' => $firstItem->member_type,
                    'phone' => $firstItem->phone,
                    'job_title' => $firstItem->jobTitle,
                    'department' => $firstItem->department,
                    'hospital' => $firstItem->hospital,
                    'bayanati_number' => $firstItem->bayanati_number,
                    'speciality' => $firstItem->speciality,
                    'code_id' => $firstItem->code_id,
                    'user_status' => $firstItem->user_status,

                    // Total number of scans/check-ins for this user on this day
                    'scan_count' => $items->count(),

                    // Each attendance record with its ID and time
                    'attendance' => $items
                        ->map(function ($item) {
                            return [
                                'id' => $item->attendee_id,
                                'time' => Carbon::parse($item->created_at)
                                    ->format('Y-m-d H:i:s'),
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values();

        $formattedData->put($dayName, [
            'date' => $date,

            // Unique attendee count
            'attendee_count' => $formattedAttendees->count(),

            // Total scan count for the day
            'scan_count' => $dayRecords->count(),

            'attendees' => $formattedAttendees,
        ]);
    }

    // Check if there are no attendance records at all
    if ($attendees->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No attendees found',
            'status_code' => 404,
            'data' => $formattedData,
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'Success',
        'status_code' => 200,
        'data' => $formattedData,
    ], 200);
}
public function deleteAttendee(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:attendees,id',
    ]);

    $deleted = DB::table('attendees')
        ->where('id', $request->id)
        ->delete();

    if (!$deleted) {
        return response()->json([
            'status' => false,
            'message' => 'Attendance record could not be deleted',
            'status_code' => 500,
        ], 500);
    }

    return response()->json([
        'status' => true,
        'message' => 'Attendance record deleted successfully',
        'status_code' => 200,
    ], 200);
}

public function storeBadgePrint(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'type' => 'required|string|max:255',
    ]);

    $badgePrintId = DB::table('badge_prints')->insertGetId([
        'user_id' => $request->user_id,
        'type' => $request->type,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Badge print recorded successfully',
        'status_code' => 200,
        'data' => [
            'id' => $badgePrintId,
            'user_id' => $request->user_id,
            'type' => $request->type,
        ],
    ], 200);
}


public function getBadgePrintStats()
{
    $totalBadgePrints = DB::table('badge_prints')->count();

    $uniqueUsersPrinted = DB::table('badge_prints')
        ->distinct('user_id')
        ->count('user_id');

    return response()->json([
        'status' => true,
        'message' => 'Success',
        'status_code' => 200,
        'data' => [
            'total_badges_printed' => $totalBadgePrints,
            'unique_users_printed' => $uniqueUsersPrinted,
        ],
    ], 200);
}

public function getUserByEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $user = DB::table('users')
        ->select(
            'name',
            'email',
            'hospital',
            'department',
            'speciality',
            'profession',
            'jobTitle',
            'phone'
        )
        ->where('email', $request->email)
        ->first();

    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found',
            'status_code' => 404,
            'data' => null,
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'User details retrieved successfully',
        'status_code' => 200,
        'data' => [
            'name' => $user->name,
            'email' => $user->email,
            'hospital' => $user->hospital,
            'department' => $user->department,
            'speciality' => $user->speciality,
            'profession' => $user->profession,
            'job_title' => $user->jobTitle,
            'phone' => $user->phone,
        ],
    ], 200);
}

}
