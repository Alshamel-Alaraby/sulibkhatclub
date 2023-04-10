<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Branch extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_branches';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'App\Enums\IsActive',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }

    public function children()
    {
        return $this->hasMany(Branch::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }

    public function hasChildren()
    {
        return $this->stores()->count() > 0 || $this->serials()->count() > 0 || $this->children()->count() > 0 ;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Branch')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
