<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class  QuestionBankResource extends JsonResource
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
            'note' => $this->note,
            'categoryId' => $this->categoryId,
            'creatorId' => $this->creatorId,
            'created_at' =>  strtotime($this->created_at),
            'updated_at' =>  strtotime($this->updated_at),
        ];
    }
}
