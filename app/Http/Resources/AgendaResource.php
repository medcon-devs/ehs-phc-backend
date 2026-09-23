<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JsonSerializable;

class AgendaResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'agenda_date' => $this->agenda_date,
            'details' => $this->getDetails($this)
        ];
    }

    function getDetails($obj): array
    {
        $details = $obj->details()->where(['agenda_type'=> false])->orderBy(DB::raw('CAST(`order` AS SIGNED)'), 'asc')->get();
        $res = [];
        foreach ($details as $item) {
            $res[$item->agenda_time][] = [
                'id' => $item->id,
                'agenda_time' => $item->agenda_time,
                'type' => $item->type,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'colored' => $item->colored,
            ];
        }
        return $res;
    }
}
