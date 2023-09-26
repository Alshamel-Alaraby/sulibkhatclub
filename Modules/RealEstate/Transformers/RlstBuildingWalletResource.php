<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstBuildingWalletResource extends JsonResource
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
            "id"       => $this->id,
            "wallet"   => $this->whenLoaded('wallet'),
            "building" => $this->whenLoaded('building'),
            "bu_ty"    => $this->bu_ty,

        ];
    }
}
