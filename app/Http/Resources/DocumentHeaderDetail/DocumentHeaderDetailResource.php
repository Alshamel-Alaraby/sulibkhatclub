<?php

namespace App\Http\Resources\DocumentHeaderDetail;

use App\Http\Resources\DocumentHeader\DocumentHeaderResource;
use App\Http\Resources\Governorate\GovernorateResource;
use App\Http\Resources\ItemBreakDown\ItemBreakDownResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\PackageResource;

class DocumentHeaderDetailResource extends JsonResource
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
            'id'                 => $this->id,
            'document_header_id' => $this->document_header_id,
            'category_id'        => $this->category_id,
            'date_from'          => $this->date_from,
            'date_to'            => $this->date_to,
            'governorate_id'     => $this->governorate_id,
            'quantity'           => $this->quantity,
            'package_id'         => $this->package_id,
            'price_per_uint'     => $this->price_per_uint,
            'total'              => $this->total,
            'unit_type'          => $this->unit_type,
            'is_stripe'          => $this->is_stripe,
//            'governorate'        => new GovernorateResource($this->governorate) ,
//            'document_header'    =>  new DocumentHeaderResource($this->documentHeader) ,
//            'package'            =>  new PackageResource($this->package) ,

            'item_break_downs'            =>   ItemBreakDownResource::collection($this->itemBreakDowns) ,



        ];
    }
}
