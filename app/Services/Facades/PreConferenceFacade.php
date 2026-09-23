<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\PreConference;
use App\Services\Interfaces\PreConferenceInterface;

class PreConferenceFacade extends BaseFacade implements PreConferenceInterface
{
    public function __construct()
    {
        $this->setModel(PreConference::class);
        $this->setColumns([
            'agenda_date', 'event_id', 'order',
        ]);
        $this->setRules([
            "agenda_date" => _RuleHelper::_Rule_Require,
            "event_id" => _RuleHelper::_Rule_Require,
        ]);
        $this->setWhere([]);
        $this->setOrderColumn('order');
        $this->setOrderBy("asc");
    }
}
