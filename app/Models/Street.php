<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_streets';

    protected $fillable = ['name', 'name_e', "avenue_id", 'is_active'];

    protected $casts = [
        // 'is_active' => '\App\Enums\IsActive',
    ];

    public function avenue()
    {
        return $this->belongsTo(Avenue::class);
    }

    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class);
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Street')
            ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
