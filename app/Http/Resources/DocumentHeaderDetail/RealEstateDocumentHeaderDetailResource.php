<?php

namespace App\Http\Resources\DocumentHeaderDetail;

use App\Http\Resources\GeneralItemResource;
use App\Http\Resources\Governorate\GovernorateResource;
use App\Http\Resources\ItemBreakDown\ItemBreakDownResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\PackageResource;
use Modules\Booking\Transformers\UnitResource;

class RealEstateDocumentHeaderDetailResource extends JsonResource
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
            'document_header_id' => $this->document_header_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'quantity' => $this->quantity,
            'price_per_uint' => $this->price_per_uint,
            'total' => $this->total,
            'unit_type' => $this->unit_type,
            'rent_days' => $this->rent_days,
            'unit_id' => $this->unit_id,
            'discount' => $this->discount,
            'sell_method_discount' => $this->sell_method_discount,
            'sell_method_id' => $this->sell_method_id,
            'invoice_discount' => $this->invoice_discount,
            'net_invoice' => $this->net_invoice,
            'unrealized_revenue' => $this->unrealized_revenue,
            'external_commission' => $this->external_commission,
            'revenue' => $this->revenue,
            'unrealized_commission' => $this->unrealized_commission,
            'commission' => $this->commission,
            'category' => $this->category,
            'note' => $this->note,
            'prefix_related' => $this->prefix_related,
            'break_downs' => ItemBreakDownResource::collection($this->itemBreakDowns),
            'unit' => $this->realEstateUnit,
            'building' => $this->building,
        ];
    }
}
