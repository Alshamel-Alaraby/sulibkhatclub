<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Archiving\Entities\DocumentField;
use Spatie\Activitylog\LogOptions;

class TreeProperty extends Model
{
    use HasFactory, LogTrait   ;

    protected $table="general_tree_properties";

    protected $guarded = ['id'];
    protected $appends = ['haveChildren'];

    public function unitContracts()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstUnitContract::class, 'unit_code');
    }

    public function parent()
    {
        return $this->belongsTo(TreeProperty::class, 'parent_id');
    }

    public function screenTreeProperties()
    {
        return $this->hasMany(ScreenTreeProperty::class, 'property_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Tree Property')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function children()
    {
        return $this->hasMany(TreeProperty::class, 'parent_id');
    }

    public function documentFields()
    {
        return $this->hasMany(DocumentField::class, 'tree_property_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->unitContracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'unit Contracts',
                'count' => $this->unitContracts()->count(),
                'ids' => $this->unitContracts()->pluck('unit_code')->toArray(),
            ];
        }
        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('name')->toArray(),
            ];
        }
        if ($this->documentFields()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'document Fields',
                'count' => $this->documentFields()->count(),
                'ids' => $this->documentFields()->pluck('name')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

}
