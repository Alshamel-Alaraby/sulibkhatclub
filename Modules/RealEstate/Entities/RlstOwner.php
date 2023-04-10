<?php

namespace Modules\RealEstate\Entities;

use App\Models\Country;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class RlstOwner extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'phone',
        'email',
        'country_id',
        'city_id',
        'rb_code',
        'phone_code',
        'nationality_id',
        'bank_account_id',
        'contact_person',
        'contact_phones',
        'national_id',
        'whatsapp',
        'categories',
        'attachments',

    ];

    // relations
    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function nationality()
    {
        return $this->belongsTo(\App\Models\Country::class, 'nationality_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(\App\Models\BankAccount::class);
    }

    public function wallets()
    {

        return $this->belongsToMany(\Modules\RealEstate\Entities\RlstWallet::class, 'rlst_wallet_owners', 'wallet_id', 'owner_id')
            ->withPivot('percentage');

    }
    // attributes

    protected function categories(): Attribute
    {
        return Attribute::make(
            get:fn($value) => json_decode($value),
            set:fn($value) => json_encode($value),
        );
    }

    protected function attachments(): Attribute
    {
        return Attribute::make(
            get:fn($value) => json_decode($value),
            set:fn($value) => json_encode($value),
        );
    }

    public function getPercentageAttribute()
    {
        $percentage = 0;
        foreach ($this->wallets as $wallet) {
            $percentage += $wallet->pivot->percentage;
        }
        return $percentage;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Real Estate Owners')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
