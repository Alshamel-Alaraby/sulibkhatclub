<?php

namespace App\Http\Resources\GeneralCustomer;

use App\Http\Resources\BankAccount\BankAccountResource;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\CustomerGroup\CustomerGroupResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\Salesman\SalesmanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralCustomerResource extends JsonResource
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
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'rp_code' => $this->rp_code,
            'nationality' => $this->nationality,
            'bank_account_id' => $this->bank_account_id,
            'contact_person' => $this->contact_person,
            'contact_phone' => $this->contact_phone,
            'national_id' => $this->national_id,
            'whatsapp' => $this->whatsapp,
            'passport_no' => $this->passport_no,
            'note' => $this->note,
            'code_country' => $this->code_country,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'snapchat' => $this->snapchat,
            'twitter' => $this->twitter,
            'website' => $this->website,
            'salesman_id' => $this->salesman_id,
            'employee_id' => $this->employee_id,
            'customer_source_id' => $this->customer_source_id,
            'customer_group_id' => $this->customer_group_id,
            'is_supplier' => $this->is_supplier,
            'sector_id' => $this->sector_id,
            // 'sector' => $this->sector,
            // 'customer_source' => $this->customerSource,

            'type' => $this->type,
            'address' => $this->address,
            'id_number' => $this->id_number,
            'tax_record' => $this->tax_record,
            'passport_expiry_date' => $this->passport_expiry_date,
            'visa_no' => $this->visa_no,
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,

            'sector' => collect($this->whenLoaded('sector'))->only(['id', 'name', 'name_e']),
            'customer_source' => collect($this->whenLoaded('customerSource'))->only(['id', 'name', 'name_e']),
            'salesmen' => collect($this->whenLoaded('salesmen'))->only(['id', 'name', 'name_e']),
            'employee' => collect($this->whenLoaded('employee'))->only(['id', 'name', 'name_e']),
            'country' => collect($this->whenLoaded('country'))->only(['id', 'name', 'name_e']),
            'city' => collect($this->whenLoaded('city'))->only(['id', 'name', 'name_e']),
            'bankAccount' => collect($this->whenLoaded('bankAccount'))->only(['id', 'name', 'name_e']),
            'customerGroup' => collect($this->whenLoaded('customerGroup'))->only(['id', 'name', 'name_e']),
            'customer_main_category' => collect($this->whenLoaded('customer_main_category'))->only(['id', 'name', 'name_e']),
            'customer_sub_category' => collect($this->whenLoaded('customer_sub_category'))->only(['id', 'name', 'name_e']),


            // "salesmen" => new SalesmanResource($this->salesmen),
            // "employee" => new EmployeeResource($this->employee),
            // "country" => new CountryResource($this->country),
            // "city" => new CityResource($this->city),
            // "bankAccount" => new BankAccountResource($this->bankAccount),
            // "customerGroup" => new CustomerGroupResource($this->customerGroup),
            // 'customer_main_category' => $this->customer_main_category,
            // 'customer_sub_category' => $this->customer_sub_category,


//            'company_id'=>$this->company_id,
//            'facebook'=>$this->facebook,
//            'instagram'=>$this->instagram,
//            'linkedin'=>$this->linkedin,
//            'snapchat'=>$this->snapchat,
//            'twitter'=>$this->twitter,
//            'website'=>$this->website,
//            'salesman_id'=>$this->salesman_id,
//            'customer_source_id'=>$this->customer_source_id,
//            'sector_id'=>$this->sector_id,
//            'avenue_id'=>$this->avenue_id,

        ];
    }
}
