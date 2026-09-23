<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\EventMessage;
use App\Services\Interfaces\EventMessageInterface;

class EventMessageFacade extends BaseFacade implements EventMessageInterface
{
    public function __construct()
    {
        $this->setModel(EventMessage::class);
        $this->setColumns([
            'event_id',
            'title',
            'image',
            'subtitle',
            'message_header',
            'message_content',
            'order',
        ]);
        $this->setRules([
            "title" => _RuleHelper::_Rule_Require,
            "image" => _RuleHelper::_Rule_Require,
            "subtitle" => _RuleHelper::_Rule_Require,
            "message_header" => _RuleHelper::_Rule_Require,
            "message_content" => _RuleHelper::_Rule_Require,
            "event_id" => _RuleHelper::_Rule_Require,
        ]);
        $this->setWhere([]);
        $this->setOrderColumn('order');
        $this->setOrderBy("asc");
    }
}
