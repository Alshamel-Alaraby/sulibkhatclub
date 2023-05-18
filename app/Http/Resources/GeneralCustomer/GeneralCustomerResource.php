<?php

namespace App\Http\Resources\GeneralCustomer;

use App\Http\Resources\City\CityResource;
use App\Http\Resources\Country\CountryResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BankAccount\BankAccountResource;

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

            'id'=>$this->id,
            'name'=>$this->name,
            'name_e'=>$this->name_e,
            'phone'=>$this->phone,
            'email'=>$this->email,
            'country_id'=>$this->country_id,
            'city_id'=>$this->city_id,
            'rp_code'=>$this->rp_code,
            'nationality'=>$this->nationality,
            'bank_account_id'=>$this->bank_account_id,
            'contact_person'=>$this->contact_person,
            'contact_phone'=>$this->contact_phone,
            'national_id'=>$this->national_id,
            'whatsapp'=>$this->whatsapp,
            'passport_no'=>$this->passport_no,
            'note1'=>$this->note1,
            'note2'=>$this->note2,
            'note3'=>$this->note3,
            'note4'=>$this->note4,
            'employee_id'=>$this->employee_id,
            "employee" => new EmployeeResource($this->employee),
            "country" => new CountryResource($this->country),
            "city" => new CityResource($this->city),
            "bankAccount" => new BankAccountResource($this->bankAccount),
            "media" => isset($this->files) ? FileResource::collection($this->files) : null,
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
