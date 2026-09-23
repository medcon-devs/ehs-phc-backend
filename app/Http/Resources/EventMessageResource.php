<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EventMessageResource extends BaseResource
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
            'title' => $this->title,
            'image' => asset('storage/images/event-' . $this->event_id . '-' . date('Y', strtotime($this->event->start_date)) . '/' . $this->image),
            'subtitle' => $this->subtitle,
            'message_header' => $this->message_header,
            'message_content' => $this->message_content,
        ];
    }
}
