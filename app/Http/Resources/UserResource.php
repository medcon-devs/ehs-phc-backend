<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'memberType'=>$this->member_type,
            'phone'=>$this->phone,
            'jobTitle'=>$this->jobTitle,
            'department'=>$this->department,
            'hospital'=>$this->hospital,
            'bayanati_number'=>$this->bayanati_number,
            'speciality'=>$this->speciality,
            'code_id'=>$this->code_id,
            'code'=>$this->code()->first()?$this->code()->first()->code:"-",
            'token' => $this->createToken('API Token')->accessToken,
            'user_status' => $this->user_status,
        ];
    }
}