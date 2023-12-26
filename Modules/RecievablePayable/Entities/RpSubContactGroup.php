<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\GL\Entities\GlCoa;

class RpSubContactGroup extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];


    public function mainContactGroup(){
        return $this->belongsTo(RpMainContactGroup::class,"rp_main_contact_group_id");
    }
    public function glAccount(){
        return $this->belongsTo(GlCoa::class,"gl_acc_no");
    }

    public function rp_screen_sub_contact_group(){
        return $this->hasMany(RpScreenSubContactGroup::class,"sub_contract_group_id");
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->rp_screen_sub_contact_group()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rp_screen_sub_contact_group',
                'count' => $this->rp_screen_sub_contact_group()->count(),
                'ids' => $this->rp_screen_sub_contact_group()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }
}
