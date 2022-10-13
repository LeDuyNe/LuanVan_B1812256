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
            'arrayQuestion' => $this->arrayQuestion,
            'timeDuration' => $this->timeDuration,
            'timeStart' => $this->timeStart,
            'countLimit' => $this->countLimit,
            'numExamination' => $this->numExamination,
            'questionBankId' => $this->questionBankId,
            'creatorId' => $this->creatorId,
            'created_at' =>  strtotime($this->created_at),
            'updated_at' =>  strtotime($this->updated_at),
        ];
    }
}
