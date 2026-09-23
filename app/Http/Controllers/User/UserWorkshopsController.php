<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserWorkshopsResource;
use App\Http\Resources\BaseResource;
use App\Models\UserWorkshops;
use App\Services\Interfaces\UserWorkshopsInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\Facades\UserWorkshopFacade;


class UserWorkshopsController extends Controller
{
    private  $userWorkshopsService;

    public function __construct(UserWorkshopsInterface $userWorkshopsService)
    {
        $this->userWorkshopsService = $userWorkshopsService;
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if ($user) {
                // Use the UserWorkshopFacade to fetch the user's bookings
                $bookings = $this->userWorkshopsService->getOneByColumns([
                    'user_id' => $user->id
                ]);
                if($bookings){
                // Return the bookings as a JSON response
                    return UserWorkshopsResource::create($bookings);
                }
            }
            return BaseResource::returns();
        } catch (Exception $exception) {
            // Handle any exceptions that occur
            return BaseResource::exception($exception);
        }
    }

    public function store(Request $request): JsonResponse|UserWorkshopsResource
    {
        try {
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            $check = $this->userWorkshopsService->getOneByColumns([
                'user_id' => $user->id
            ]);
            if ($check) {
                return BaseResource::return("Conflict", 409);
            }

            $userWorkshopsService = $this->userWorkshopsService->store($request);
            if ($userWorkshopsService) {
                DB::commit();
                return UserWorkshopsResource::create($userWorkshopsService);
            }
            DB::rollBack();
            return BaseResource::return();
        } catch (Exception $exception) {
            return BaseResource::return($exception->getMessage(), $exception->getCode());
        }
    }

    public function update(Request $request)
    {
        try {
            Log::info('Request data:', $request->all());
            DB::beginTransaction();
            $user = Auth::guard('api')->user();
            $check = $this->userWorkshopsService->getOneByColumns([
                'user_id' => $user->id
            ]);

            
            if ($check) {
                $check->update([
                    'option_1' => $request->input('option_1'),
                    'option_2' => $request->input('option_2')
                ]);
               
                Log::error($check);
                $check->save();
                DB::commit();
                return UserWorkshopsResource::create($check);
            }


            return BaseResource::return("Error", 400);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return BaseResource::return($exception->getMessage(), 500);
        }
    }
}
