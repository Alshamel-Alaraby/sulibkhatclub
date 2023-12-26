<?php

namespace Modules\RealEstate\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RlstTenantResource extends JsonResource
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
            'name' => $this->name,
            'name_e' => $this->name_e,
            'blacklisted' => $this->blacklisted,
            'is_whatsapp' => $this->is_whatsapp,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'nationality' => $this->whenLoaded('nationality'),
            'work_address' => $this->work_address,
            'home_address' => $this->home_address,
            'civil_no' => $this->civil_no,
            'passport_no' => $this->passport_no,
            'residence_no' => $this->residence_no,
            'chart' => $this->whenLoaded('chart'),
            'tenantType' => $this->whenLoaded('tenantType'),
            'mobile_code' => $this->mobile_code,
            'mobile' => $this->mobile,
            'email' => $this->email,
        ];
    }
}
