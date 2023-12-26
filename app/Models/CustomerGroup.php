<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $table = 'general_customer_groups';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select('id', 'title', 'title_e', 'discount', 'is_default');
    }

    public function generalCustomers()
    {
        return $this->hasMany(GeneralCustomer::class, 'customer_group_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'customer_group_id');
    }
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->generalCustomers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'generalCustomers',
                'count' => $this->generalCustomers()->count(),
                'ids' => $this->generalCustomers()->pluck('name')->toArray(),
            ];
        }
        if ($this->suppliers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'suppliers',
                'count' => $this->suppliers()->count(),
                'ids' => $this->suppliers()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Customer Group')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
