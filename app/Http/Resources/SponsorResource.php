<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SponsorResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => asset('storage/images/event-' . $this->event->id . '-' . date('Y', strtotime($this->event->end_date)) . '/sponsors/' . $this->logo),
            'url' => asset('storage/images/event-' . $this->event->id . '-' . date('Y', strtotime($this->event->end_date)) . '/sponsors/' . $this->logo)
        ];
    }
}
