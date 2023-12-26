<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;
use Spatie\Activitylog\LogOptions;

class Salesman extends Model
{
    use HasFactory, SoftDeletes, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'salesman_type_id',
        "company_id",
    ];

    public function scopeData($query)
    {
        return $query->select(
            'name',
            'name_e',
            'salesman_type_id',
        )->with(['salesmanType:id,name,name_e']);
    }

    protected $table = "general_salesmen";

    // relations
    public function salesmanType()
    {
        return $this->belongsTo(SalesmenType::class, 'salesman_type_id');
    }

    public function reservations()
    {
        return $this->hasMany(RlstReservation::class);
    }

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function customers()
    {
        return $this->hasMany(GeneralCustomer::class, 'salesman_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'salesman_id');
    }
    public function contracts()
    {
        return $this->hasMany(RlstContract::class, 'salesman_id');
    }

    public function RlstInvoices()
    {
        return $this->hasMany(RlstInvoice::class, 'salesman_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->RlstInvoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInvoices',
                'count' => $this->RlstInvoices()->count(),
                'ids' => $this->RlstInvoices()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->contracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contracts',
                'count' => $this->contracts()->count(),
                'ids' => $this->contracts()->pluck('date')->toArray(),
            ];
        }

        if ($this->reservations()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'reservations',
                'count' => $this->reservations()->count(),
                'ids' => $this->reservations()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('date')->toArray(),
            ];
        }
        if ($this->customers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'customers',
                'count' => $this->customers()->count(),
                'ids' => $this->customers()->pluck('name')->toArray(),
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

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Salesman')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
