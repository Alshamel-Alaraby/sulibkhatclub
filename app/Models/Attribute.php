<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_attributes';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select('id', 'name', 'name_e');
    }

    public function children()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    // public function product_variant()
    // {
    //     return $this->hasMany(ProductVariant::class, 'attribute_id');
    // }

    public function documentHeader()
    {
        return $this->belongsToMany(DocumentHeader::class, 'general_attendant_document_headers', 'document_header_id', 'attendant_id');
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


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Attribute')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
