<?php

namespace Modules\RealEstate\Entities;

use App\Models\GeneralCustomer;
use App\Models\Salesman;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;

class RlstContract extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, LogTrait, MediaTrait;

    protected $fillable = [
        "salesman_id",
        "customer_id",
        "date",
        "branch_id",
        "document_id",
        "serial_id",
        "module_type",
        "start_date",
        "end_date",
        "serial_number",
        "prefix"
    ];

    // relations

    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class, 'customer_id', 'id');
    }

    public function reservation()
    {
        return $this->belongsTo(\Modules\RealEstate\Entities\RlstReservation::class, 'reservation_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstContractDetail::class, 'contract_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id', 'id');
    }

    public function serial()
    {
        return $this->belongsTo(\App\Models\Serial::class, 'serial_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(\App\Models\Document::class, 'document_id', 'id');
    }

    public function breakDowns()
    {
        return $this->hasMany(RpBreakDown::class, 'break_id');
    }

    public function hasChildren()
    {
        return $this->details()->count() > 0  ;

    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Rlst Contracts')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
