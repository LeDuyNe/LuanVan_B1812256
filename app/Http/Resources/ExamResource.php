<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
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
            'timeDuration' => $this->timeDuration,
            'timeStart' => $this->timeStart,
            'countLimit' => $this->countLimit,
            'categoryId' => $this->categoryId,
            'creatorId' => $this->creatorId
            // 'created_at' => (string)$this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
