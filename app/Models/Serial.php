<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ClubMembers\Entities\CmMemberReject;
use Modules\ClubMembers\Entities\CmTransaction;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstContractHeader;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;
use Spatie\Activitylog\LogOptions;

class Serial extends Model
{
    use HasFactory, LogTrait;
    protected $table = "general_serials";

    protected $fillable = [
        'start_no',
        'perfix',
        'suffix',
        'restart_period_id',
        'company_id',
        'document_id',
        "branch_id",
        "name",
        "name_e",
        "gender",
    ];

    public function scopeData($query)
    {
        return $query->select(
            'id',
            'start_no',
            'perfix',
            'suffix',
            'restart_period_id',
            'document_id',
            "branch_id",
            "name",
            "name_e",
            "gender",
        )->with(['branch:id,name,name_e','document:id,name,name_e','restartPeriod:id,name,name_e']);
    }


    protected $appends = [
        'has_child',
    ];

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function invoices()
    {
        return $this->hasMany(RlstInvoice::class, 'serial_id');
    }

    public function documentHeaders()
    {
        return $this->hasMany(DocumentHeader::class, 'serial_id');
    }

    public function cm_member_rejects()
    {
        return $this->hasMany(CmMemberReject::class, 'serial_id');
    }

    public function cm_transactions()
    {
        return $this->hasMany(CmTransaction::class, 'serial_id');
    }

    public function contracts()
    {
        return $this->hasMany(RlstContract::class, 'serial_id');
    }

    public function contractHeaders()
    {
        return $this->hasMany(RlstContractHeader::class, 'serial_id');
    }

    public function reservations()
    {
        return $this->hasMany(RlstReservation::class,'serial_id');
    }



    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->cm_member_rejects()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm member rejects',
                'count' => $this->cm_member_rejects()->count(),
                'ids' => $this->cm_member_rejects()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->reservations()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'reservations',
                'count' => $this->reservations()->count(),
                'ids' => $this->reservations()->pluck('id')->toArray(),
            ];
        }
        if ($this->contractHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractHeaders',
                'count' => $this->contractHeaders()->count(),
                'ids' => $this->contractHeaders()->pluck('id')->toArray(),
            ];
        }
        if ($this->cm_transactions()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm transactions',
                'count' => $this->cm_transactions()->count(),
                'ids' => $this->cm_transactions()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->contracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contracts',
                'count' => $this->contracts()->count(),
                'ids' => $this->contracts()->pluck('date')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('date')->toArray(),
            ];
        }
        if ($this->invoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'invoices',
                'count' => $this->invoices()->count(),
                'ids' => $this->invoices()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->documentHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeaders',
                'count' => $this->documentHeaders()->count(),
                'ids' => $this->documentHeaders()->pluck('prefix')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }


    public function getHasChildAttribute()
    {
        if ($this->invoices()->count() > 0) {
            return 1;
        }
        return 0;
    }

    public function restartPeriod()
    {
        return $this->belongsTo(RestartPeriod::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Serial')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
