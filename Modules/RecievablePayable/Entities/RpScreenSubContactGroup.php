<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpScreenSubContactGroup extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

  

    public function subContactGroup(){
        return $this->belongsTo(RpSubContactGroup::class,"sub_contract_group_id");
    }
}
