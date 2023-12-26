<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;


class RlstTenantType extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_tenant_types';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e');
    }


    public function tenants()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstTenant::class, 'tenant_type_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->tenants()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'tenants',
                'count' => $this->tenants()->count(),
                'ids' => $this->tenants()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Tenant Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
