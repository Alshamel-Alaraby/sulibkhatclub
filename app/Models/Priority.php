<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Priority extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_priorities';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'name',
            'name_e',
            'parent_id',
            'is_valid',
            'is_default'
        )->with('parent:id,name,name_e');
    }

    protected $appends = ['haveChildren'];

    // relations

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function parent()
    {
        return $this->belongsTo(Priority::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Priority::class, 'parent_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'priority_id');
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
        if ($this->tasks()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'tasks',
                'count' => $this->tasks()->count(),
                'ids' => $this->tasks()->pluck('contact_person')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function locations()
    {
        return $this->hasMany(Location::class, 'priority_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Priority')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
