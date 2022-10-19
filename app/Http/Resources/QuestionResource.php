<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'content' => $this->content,
            'level' => $this->level,
            'topQuestionsId' => $this->top_question_ids,
            'bottomQuestionsId' => $this->bottom_question_ids,
        ];
    }
}
