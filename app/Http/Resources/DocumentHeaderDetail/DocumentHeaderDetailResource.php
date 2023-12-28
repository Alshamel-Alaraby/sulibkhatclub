<?php

namespace App\Http\Resources\DocumentHeaderDetail;

use App\Http\Resources\GeneralItemResource;
use App\Http\Resources\Governorate\GovernorateResource;
use App\Http\Resources\ItemBreakDown\ItemBreakDownResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Transformers\PackageResource;
use Modules\Booking\Transformers\UnitResource;

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
            'id' => $this->id,
            'document_header_id' => $this->document_header_id,
            'category_id' => $this->category_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'governorate_id' => $this->governorate_id,
            'quantity' => $this->quantity,
            'package_id' => $this->package_id,
            'price_per_uint' => $this->price_per_uint,
            'total' => $this->total,
            'unit_type' => $this->unit_type,
            'is_stripe' => $this->is_stripe,
            'rent_days' => $this->rent_days,
            'building_id' => $this->building_id,
            'unit_id' => $this->unit_id,
            'item_id' => $this->item_id,
            'category_booking' => $this->category_booking,


            'check_in_time' => $this->check_in_time,
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
            'company_insurance_amount' => $this->when($this->company_insurance_amount , $this->company_insurance_amount),
            'patient_amount' => $this->when($this->patient_amount,$this->patient_amount),
            'service_id' => $this->when($this->service_id,$this->service_id),

            'governorate' => new GovernorateResource($this->governorate),
//            'document_header'    =>  new DocumentHeaderResource($this->documentHeader) ,
            'package' => new PackageResource($this->package),

            'break_downs' => ItemBreakDownResource::collection($this->itemBreakDowns),

            'unit' => $this->unit,
            'item' => $this->item,
            'service' => $this->service,
            'patient_amount' => $this->patient_amount,
            'company_insurance_amount' => $this->company_insurance_amount,
        ];
    }
}
