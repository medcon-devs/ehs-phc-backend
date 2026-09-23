<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class UserWorkshopsResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "option_1" => $this->option_1,
            "option_2" => $this->option_2,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}