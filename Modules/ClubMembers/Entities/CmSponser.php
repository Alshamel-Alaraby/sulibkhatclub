<?php

namespace Modules\ClubMembers\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class CmSponser extends Model
{
    use HasFactory, LogTrait, SoftDeletes;


    protected $fillable = [
        'name',
        'name_e',
        'parent_id',

    ];

    protected $appends = ['haveChildren'];

    protected $table = 'cm_sponsers';

    public function children()
    {
        return $this->hasMany(CmSponser::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(CmSponser::class, 'parent_id', 'id');
    }

    public function getHaveChildrenAttribute()
    {
        return static::where("parent_id", $this->id)->count() > 0;
    }

    public function members(){
        return $this->hasMany(\Modules\ClubMembers\Entities\CmMember::class, 'sponsor_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Sponser')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function hasChildren()
    {
        $h = $this->members()->count() > 0;
        return $h;
    }
}
