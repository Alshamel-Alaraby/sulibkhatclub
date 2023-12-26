<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ClubMembers\Entities\CmTransaction;
use Modules\RealEstate\Entities\RlstContract;
use Modules\RealEstate\Entities\RlstContractHeader;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Entities\RlstReservation;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Entities\RpPaymentPlanInstallment;

class Document extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_documents';

    protected $guarded = [''];

    protected $appends = ['relation_voucher_header'];

    protected $casts = ["attributes" => "json"];


    public function break_settlements()
    {
        return $this->hasMany(BreakSettlement::class, 'document_id');
    }
    public function payment_plan_installments()
    {
        return $this->hasMany(RpPaymentPlanInstallment::class, 'doc_type_id');
    }

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class, 'doc_type_id');
    }

    public function documentHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'document_id');
    }
    public function documentRelatedHeader()
    {
        return $this->hasMany(DocumentHeader::class, 'related_document_id');
    }

    public function documentRelateds()
    {
        return $this->belongsToMany(Document::class, 'general_document_related', 'document_id', 'document_related_id','id','id');
    }

    public function clientTypes()
    {
        return $this->belongsToMany(ClientType::class, 'general_client_types_documents', 'document_id', 'client_type_id','id','id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'general_documents_approve_personal', 'document_id', 'employee_id','id','id');
    }

    public function voucherHeader()
    {
        return $this->hasMany(VoucherHeader::class, 'document_id');

    }

    public function documentModuleType()
    {
        return $this->belongsTo(DocumentModuleType::class, 'document_module_type_id');
    }

    public function getRelationVoucherHeaderAttribute($key)
    {
        return $this->voucherHeader()->count() > 0 ? 1 : 0;
    }

    public function documentCompanyModuleStatuses()
    {
        return $this->hasMany(DocumentCompanyModuleStatus::class,'document_id');

    }
    public function serials()
    {
        return $this->hasMany(Serial::class,'document_id');

    }

    public function cm_transactions()
    {
        return $this->hasMany(CmTransaction::class, 'document_id');
    }

    public function rp_break_downs()
    {
        return $this->hasMany(RpBreakDown::class, 'document_id');
    }

    public function contracts()
    {
        return $this->hasMany(RlstContract::class, 'document_id');
    }

    public function contractHeaders()
    {
        return $this->hasMany(RlstContractHeader::class, 'document_id');
    }

    public function RlstInvoices()
    {
        return $this->hasMany(RlstInvoice::class, 'document_id');
    }

    public function reservations()
    {
        return $this->hasMany(RlstReservation::class,'document_id');
    }




    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->rp_break_downs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'rp break downs',
                'count' => $this->rp_break_downs()->count(),
                'ids' => $this->rp_break_downs()->pluck('rate')->toArray(),
            ];
        }
        if ($this->reservations()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'reservations',
                'count' => $this->reservations()->count(),
                'ids' => $this->reservations()->pluck('id')->toArray(),
            ];
        }
        if ($this->RlstInvoices()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'RlstInvoices',
                'count' => $this->RlstInvoices()->count(),
                'ids' => $this->RlstInvoices()->pluck('id')->toArray(),
            ];
        }
        if ($this->contractHeaders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contractHeaders',
                'count' => $this->contractHeaders()->count(),
                'ids' => $this->contractHeaders()->pluck('id')->toArray(),
            ];
        }
        if ($this->contracts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contracts',
                'count' => $this->contracts()->count(),
                'ids' => $this->contracts()->pluck('date')->toArray(),
            ];
        }
        if ($this->payment_plan_installments()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'payment_plan_installments',
                'count' => $this->payment_plan_installments()->count(),
                'ids' => $this->payment_plan_installments()->pluck('v_date')->toArray(),
            ];
        }
        if ($this->cm_transactions()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'cm_transactions',
                'count' => $this->cm_transactions()->count(),
                'ids' => $this->cm_transactions()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->orders()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'orders',
                'count' => $this->orders()->count(),
                'ids' => $this->orders()->pluck('date')->toArray(),
            ];
        }
        if ($this->documentHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentHeader',
                'count' => $this->documentHeader()->count(),
                'ids' => $this->documentHeader()->pluck('prefix')->toArray(),
            ];
        }

        if ($this->documentRelatedHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentRelatedHeader',
                'count' => $this->documentRelatedHeader()->count(),
                'ids' => $this->documentRelatedHeader()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->voucherHeader()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'voucherHeader',
                'count' => $this->voucherHeader()->count(),
                'ids' => $this->voucherHeader()->pluck('prefix')->toArray(),
            ];
        }
        if ($this->documentCompanyModuleStatuses()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'documentCompanyModuleStatuses',
                'count' => $this->documentCompanyModuleStatuses()->count(),
                'ids' => $this->documentCompanyModuleStatuses()->pluck('id')->toArray(),
            ];
        }

        if ($this->break_settlements()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'break_settlements',
                'count' => $this->break_settlements()->count(),
                'ids' => $this->break_settlements()->pluck('id')->toArray(),
            ];
        }
        if ($this->serials()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'serials',
                'count' => $this->serials()->count(),
                'ids' => $this->serials()->pluck('name')->toArray(),
            ];
        }
        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Document')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

//    public function scopeCompany($query, $request)
//    {
//        return $query->where(function ($q) use ($request){
//           $q->when($request->company_id,function ($q) use ($request){
//               $q->where('company_id',$request->company_id);
//           }) ;
//        });
//    }

}
