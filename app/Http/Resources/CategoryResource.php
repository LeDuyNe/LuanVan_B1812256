<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
            'isPublished' => $this->isPublished,
            'color' => $this->color,
            'createdAt' =>  strtotime($this->created_at),
            'updatedAt' =>  strtotime($this->updated_at),
        ];
    }
}
