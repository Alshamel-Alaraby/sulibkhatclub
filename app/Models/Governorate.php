<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Panel;

class Governorate extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_governorates';

    protected $fillable = [
        'name',
        'name_e',
        'country_id',
        'is_active',
        'is_default',
        "phone_key",
        "company_id",
    ];

    protected $casts = [
        'is_active' => '\App\Enums\IsActive',
        'is_default' => '\App\Enums\IsDefault',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function avenues()
    {
        return $this->hasMany(Avenue::class);
    }
    public function customerBranches()
    {
        return $this->hasMany(\App\Models\CustomerBranch::class);
    }

    public function panels()
    {
        return $this->hasMany(Panel::class, 'governorate_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetail::class);
    }

    public function hasChildren()
    {
        return $this->avenues()->count() > 0 || $this->cities()->count() > 0 ||
        $this->panels()->count() > 0 ||
        $this->customerBranches()->count() > 0 ||
        $this->orderDetails()->count() > 0;

    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Governorate')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
