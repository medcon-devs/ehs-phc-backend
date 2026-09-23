<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Event;
use App\Services\Interfaces\EventInterface;
use Illuminate\Http\Request;
use Mockery\Exception;

class EventFacade extends BaseFacade implements EventInterface
{
    public function __construct()
    {
        $this->setModel(Event::class);
        $this->setColumns([
            'name', 'start_date', 'end_date', 'hotel', 'address', 'map', 'event_status'
        ]);
        $this->setRules([
            "name" => _RuleHelper::_Rule_Require,
            "start_date" => _RuleHelper::_Rule_Require,
            "end_date" => _RuleHelper::_Rule_Require,
            "hotel" => _RuleHelper::_Rule_Require,
            "address" => _RuleHelper::_Rule_Require,
            "map" => _RuleHelper::_Rule_Require,
            "event_status" => _RuleHelper::_Rule_Require,
        ]);
        $this->setWhere([]);
        $this->setOrderBy("asc");
    }

    public function gallery(Request $request)
    {
        return $this->getBuilder([
            'event_status' => false
        ])->orderBy('start_date', 'desc')->first();

    }
}
