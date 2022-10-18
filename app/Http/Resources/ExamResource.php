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
            'isPublished' => $this->isPublished,
            'timeDuration' => $this->timeDuration,
            'timeStart' => strtotime($this->timeStart),
            'countLimit' => $this->countLimit,
            'numExamination' => $this->numExamination,
            'creatorId' => $this->creatorId,
            'createdAt' =>  strtotime($this->created_at),
            'updatedAt' =>  strtotime($this->updated_at),
        ];
    }
}
