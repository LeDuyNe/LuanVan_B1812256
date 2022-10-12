<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avartar' => $this->avartar,
            'nameTitle' => $this->nameTitle,
            'created_at' =>  strtotime($this->created_at),
            'updated_at' =>  strtotime($this->updated_at),
        ];
    }   
}
