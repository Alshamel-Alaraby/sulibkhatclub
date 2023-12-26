<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstTenant extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_tenants';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'blacklisted','is_whatsapp', 'gender', 'marital_status', 'country_id', 'work_address', 'home_address', 'civil_no', 'passport_no', 'residence_no', 'chart_id', 'tenant_type_id', 'mobile_code', 'mobile', 'email')
            ->with(['nationality:id,name,name_e','chart:id,name,name_e', 'tenantType:id,name,name_e']);
    }

    public function nationality()
    {
        return $this->belongsTo(\App\Models\Country::class, 'country_id');
    }

    public function chart()
    {
        return $this->belongsTo(\App\Models\GlChart::class, 'chart_id');
    }

    public function tenantType()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstTenantType::class, 'tenant_type_id');
    }

    public function contractHeaders()
    {
        return $this->hasMany(RlstContractHeader::class, 'tenant_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->contractHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractHeaders',
                'count' => $this->contractHeaders()->count(),
                'ids' => $this->contractHeaders()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Tenant')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
