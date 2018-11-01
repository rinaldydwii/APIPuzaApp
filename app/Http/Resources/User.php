<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'username'      => $this->username,
            'email'         => $this->email,
            'phone_number'  => $this->phone_number,
            'avatar'        => $this->avatar ? env('ASSETS_DOMAIN').env('ASSETS_AVATAR_FOLDER').$this->avatar : $this->avatar, 
            'position_id'   => $this->position_id,
            'position_name' => $this->position_name,
        ];
    }
}