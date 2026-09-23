<?php

namespace App\Services\Facades;

use App\Helper\_RuleHelper;
use App\Models\PreConference;
use App\Models\PreConferenceDetails;
use App\Services\Interfaces\PreConferenceDetailsInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PreConreferenceDetailsFacade extends BaseFacade implements PreConferenceDetailsInterface
{
    public function __construct()
    {
        $this->setModel(PreConferenceDetails::class);
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
        return PreConferenceDetails::query()->create([
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
