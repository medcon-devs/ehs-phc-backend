<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\AgendaDetails;
use App\Services\Interfaces\AgendaDetailsInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AgendaDetailsFacade extends BaseFacade implements AgendaDetailsInterface
{
    public function __construct()
    {
        $this->setModel(AgendaDetails::class);
        $this->setColumns([
            'agenda_time', 'type', 'title', 'subtitle', 'colored', 'order', 'agenda_id'
        ]);
        $this->setRules([
            "agenda_time" => _RuleHelper::_Rule_Require,
            "type" => _RuleHelper::_Rule_Require,
            "title" => _RuleHelper::_Rule_Require,
            "order" => _RuleHelper::_Rule_Require,
            "agenda_id" => _RuleHelper::_Rule_Require,
        ]);
        $this->setWhere([]);
        $this->setOrderColumn('order');
        $this->setOrderBy("asc");
    }

    public function add(Request $request, $id): Model|null
    {
        $request->validate($this->getRules());
        return AgendaDetails::query()->create([
            'agenda_time' => $request->input('agenda_time'),
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'colored' => $request->input('colored'),
            'order' => $request->input('order'),
            'agenda_id' => $id,
        ]);
    }
}
