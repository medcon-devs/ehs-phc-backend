<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PreConferenceDetailsResource extends BaseResource
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
            'agenda_time' => $this->agenda_time,
            'type' => $this->type,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'colored' => $this->colored,
        ];
    }
}
