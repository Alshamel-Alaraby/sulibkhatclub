<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSDrugResource extends JsonResource
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
            'trade_name' => $this->trade_name,
            'trade_name_e' => $this->trade_name_e,
            'generic_name' => $this->generic_name,
            'generic_name_e' => $this->generic_name_e,
            'sell_price' => $this->sell_price,
            'note' => $this->note,
        ];
    }
}
