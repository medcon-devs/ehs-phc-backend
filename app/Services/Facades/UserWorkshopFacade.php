<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\User;
use App\Services\Interfaces\UserInterface;
use App\Services\Interfaces\UserWorkshopsInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserWorkshops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserWorkshopFacade extends BaseFacade implements UserWorkshopsInterface
{
    public function __construct()
    {
        $this->setModel(UserWorkshops::class);
        $this->setColumns([
            "user_id", "option_1", "option_2", "created_at", "updated_at"
            
        ]);
        // $this->setRules([
        //     "option_1" => _RuleHelper::_Rule_Require,
        //     "option_2" => _RuleHelper::_Rule_Require,
        // ]);
        $this->setWhere([]);
        $this->setUnique(true);
        $this->setOrderBy("asc");
    }
    public static function getUserBooking($userId)
    {
        $bookings = self::getOneByColumns(['user_id', $userId]);

        return $bookings;
    }


    function store(Request $request){
        // Validate the request data
        $request->validate($this->getRules());

        // Retrieve the workshop names or identifiers for option_1 and option_2
        $workshopNames = [
            'option_1' => $request->input('option_1'), // Replace with actual workshop name or identifier
            'option_2' => $request->input('option_2'), // Replace with actual workshop name or identifier
        ];

        // Initialize an array to hold error messages
        $errorMessages = [];

        // Count the occurrences of option_1 and option_2
        $option1Count = UserWorkshops::query()
            ->where('option_1', $request->input('option_1'))
            ->count();

        $option2Count = UserWorkshops::query()
            ->where('option_2', $request->input('option_2'))
            ->count();

        // Check if option_1 count is 25 or more and add an error message if so
        if ($option1Count >= 25) {
            $errorMessages[] = $workshopNames['option_1'];
        }

        // Check if option_2 count is 25 or more and add an error message if so
        if ($option2Count >= 25) {
            $errorMessages[] = $workshopNames['option_2'];
        }

        // If there are any error messages, construct an aggregated message
        if (!empty($errorMessages)) {
            $lastMessage = array_pop($errorMessages);
            $aggregatedMessage = implode(', ', $errorMessages);
            if (!empty($aggregatedMessage)) {
                $aggregatedMessage .= ' and ' . $lastMessage;
            } else {
                $aggregatedMessage = $lastMessage;
            }
            // Construct the final error message
            $finalMessage = "Booking is filled for " . $aggregatedMessage;
            throw new Exception($finalMessage, 409);
        }

        // Prepare the data for insertion
        $columns=[
            'user_id' => Auth::guard('api')->id(),
            'option_1' => $request->input('option_1'),
            'option_2' => $request->input('option_2'),
        ];

        // Create a new record in the UserWorkshops model
        return UserWorkshops::query()->create($columns);
    }
    
    
        public function update(Request $request, $id): array
        {
            // Attempt to find the workshop booking by its ID
            $workshopBooking = $this->getOneByColumns(["id", $id]);
        
            if ($workshopBooking) {
                      
                // Validate the request data
                $request->validate($this->getRules());
        
                // Update the workshop booking based on the request data
                $workshopBooking->option_1 = $request->input('option_1');
                $workshopBooking->option_2 = $request->input('option_2');
                $workshopBooking->save();
        
                // Return true and the updated workshop booking object
                return [true, $workshopBooking];
            }
        
            // If the workshop booking is not found, return true and null
            return [true, null];
        }
    

    
}