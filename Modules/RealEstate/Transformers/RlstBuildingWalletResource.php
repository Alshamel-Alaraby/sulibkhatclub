<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RealEstate\Transformers\RlstWalletResource;
use Modules\RealEstate\Transformers\RlstBuildingResource;
use Modules\RealEstate\Entities\RlstBuilding;
use Modules\RealEstate\Entities\RlstWallet;

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
            "id"                   => $this->id,
            // "wallet"               => $this->whenLoaded('wallet'),
            "wallet"                => RlstWallet::find($this->wallet_id)->only(['id','name','name_e']),
            "building"              => RlstBuilding::find($this->building_id)->only(['id','name','name_e']),
            // "building"             => $this->whenLoaded('building')
        ];
    }
}
