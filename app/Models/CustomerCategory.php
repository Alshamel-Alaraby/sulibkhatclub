<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];
    protected $table = 'general_customer_categories';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e', 'parent_id')
            ->with('parent:id,name,name_e');
    }

    public function parent()
    {
        return $this->belongsTo(CustomerCategory::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(CustomerCategory::class, 'parent_id');
    }

    public function customer_main_category()
    {
        return $this->hasMany(GeneralCustomer::class, 'customer_main_category_id');
    }
    public function customer_sub_category()
    {
        return $this->hasMany(GeneralCustomer::class, 'customer_sub_category_id');
    }

 
    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('name')->toArray(),
            ];
        }
        if ($this->customer_main_category()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customer_main_category',
                'count' => $this->customer_main_category()->count(),
                'ids' => $this->customer_main_category()->pluck('name')->toArray(),
            ];
        }

        if ($this->customer_sub_category()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customer_sub_category',
                'count' => $this->customer_sub_category()->count(),
                'ids' => $this->customer_sub_category()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Customer Category')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
