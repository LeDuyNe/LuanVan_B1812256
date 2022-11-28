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
            'isPublished' => $this->isPublished,
            'categoryId' => $this->categoryId,
            'creatorId' => $this->creatorId
        ];
    }
}
