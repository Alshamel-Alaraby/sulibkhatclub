<?php

namespace Modules\RealEstate\Entities;

use App\Models\Branch;
use App\Models\Document;
use App\Models\Employee;
use App\Models\ExternalSalesmen;
use App\Models\Serial;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\RecievablePayable\Entities\RpBreakDown;

class RlstContractHeader extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_contract_headers';

    protected $guarded = ['id'];


    public function scopeData($query)
    {
        return $query->select('id', 'branch_id', 'date', 'serial_id', 'related_document_id','related_document_number','related_document_prefix','employee_id','tenant_id','notice_period','insurance_amount','attendants','automatic_renews','external_salesmen_id','commission','posted','receipt_print_detail','document_id','prefix','serial_number')
            ->with([
                'branch:id,name,name_e',
                'serial',
                'relatedDocument',
                'employee:id,name,name_e',
                'tenant:id,name,name_e',
                'externalSalesmen:id,name,name_e',
                'document:id,name,name_e',
                'contractHeaderDetail',
                'units',
            ]);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class,'serial_id');
    }

    public function relatedDocument()
    {
        return $this->belongsTo(RlstContractHeader::class,'related_document_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function tenant()
    {
        return $this->belongsTo(RlstTenant::class,'tenant_id');
    }

    public function externalSalesmen()
    {
        return $this->belongsTo(ExternalSalesmen::class,'external_salesmen_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class,'document_id');
    }

    public function units()
    {

        return $this->belongsToMany(RlstUnitService::class,'rlst_contract_headers_units','contract_header_id','unit_id')->withPivot('unit_services','id');
    }

    public function contractHeaderDetail()
    {
        return $this->hasMany(RlstContractHeaderDetail::class,'contract_header_id');
    }


    public function rpBreakDowns()
    {
        return $this->hasMany(RpBreakDown::class, 'break_id','id')->where('break_type','contractHeader');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->contractHeaderDetail()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'contract Header Detail',
                'count' => $this->contractHeaderDetail()->count(),
                'ids' => $this->contractHeaderDetail()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('ContractHeader')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
