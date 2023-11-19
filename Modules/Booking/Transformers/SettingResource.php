<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->id == 1 || $this->id == 2 ? Carbon::createFromTimeString($this->value)->format('H:i') : $this->value, // Format the time as "09:00"
        ];
    }
}
