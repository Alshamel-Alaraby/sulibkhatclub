<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $typeText = '';
        switch ($this->type) {
            case 0:
                $typeText = 'رسالة تأخير';
                break;
            case 1:
                $typeText = 'رسالة اضافه';
                break;
            case 3:
                $typeText = 'رسالة غياب';
                break;
        }


        return [
            'id' => $this->id,
            'content' => $this->content,
            'content_e' => $this->content_e,
            'type'=> $typeText,

        ];
    }
}
