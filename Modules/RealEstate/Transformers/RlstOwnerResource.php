<?php

namespace Modules\RealEstate\Transformers;

use App\Http\Resources\BankAccount\BankAccountResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RlstOwnerResource extends JsonResource
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
            'phone' => $this->phone,
            'email' => $this->email,
            "country" => new CountryResource($this->country),
            "city" => new CityResource($this->city),
            'rb_code' => $this->rb_code,
            'phone_code' => $this->phone_code,
            "nationality" => new CountryResource($this->nationality),
            "ban1k_account" => new BankAccountResource($this->bankAccount),
            "contact_person" => $this->contact_person,
            "contact_phones" => $this->contact_phones,
            "national_id" => $this->national_id,
            "whatsapp" => $this->whatsapp,
            "categories" => $this->categories,
            "attachments" => $this->attachments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
