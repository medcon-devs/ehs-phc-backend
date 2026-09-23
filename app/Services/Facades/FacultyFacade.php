<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\Faculty;
use App\Services\Interfaces\FacultyInterface;

class FacultyFacade extends BaseFacade implements FacultyInterface
{
    public function __construct()
    {
        $this->setModel(Faculty::class);
        $this->setColumns([
            'name', 'subtitle', 'brief', 'biography', 'profile', 'order',
            'vip',

        ]);
        $this->setRules([
            "name" => _RuleHelper::_Rule_Require,
            "subtitle" => _RuleHelper::_Rule_Require,
            "brief" => _RuleHelper::_Rule_Require,
            "biography" => _RuleHelper::_Rule_Require,
            "profile" => _RuleHelper::_Rule_Require,
        ]);
        $this->setWhere([]);
        $this->setOrderColumn('order');
        $this->setOrderBy("asc");
    }

}
