<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceCompany extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $table = "general_insurance_companies";
    protected $guarded = ["id"];

    public function GeneralInsuranceDocuments()
    {
        return $this->hasMany(GeneralInsuranceDocument::class, 'insurance_company_id');
    }


    

}
