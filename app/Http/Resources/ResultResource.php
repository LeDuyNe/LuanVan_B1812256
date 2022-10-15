<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
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
            'numCorrect' => $this->numCorrect,
            'restTime' => $this->restTime,
            'examineeId ' => $this->examineeId ,
            'questionBankId ' => $this->questionBankId ,
            'dateSubmit' =>  strtotime($this->created_at),
        ];
    }
}
