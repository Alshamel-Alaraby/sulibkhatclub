<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RecievablePayable\Entities\RpPaymentPlanInstallment;

class VoucherHeader extends Model
{
    use HasFactory,SoftDeletes, LogTrait;


    protected $guarded = ['id'];

    protected $table = 'general_voucher_headers';

    public function scopeData($query)
    {
        return $query
            ->select(
                'id','document_id','branch_id','date','serial_id','customer_id','salesmen_id',
                'payment_method_id','client_type_id','amount','serial_number','prefix','notes','manual_document_number'
            )
            ->with([
                'document:id,name,name_e','salesmen:id,name,name_e',
                'serial:id,name,name_e','paymentMethod:id,name,name_e','clientType'
            ]);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class,'document_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function salesmen()
    {
        return $this->belongsTo(Employee::class,'salesmen_id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class,'customer_id');
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class,'serial_id');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class,'client_type_id');
    }

    public function room()
    {
        return $this->belongsTo(\Modules\Booking\Entities\Unit::class,'module_type_id');
    }

    public function breakSettlements()
    {
        return  $this->hasMany(BreakSettlement::class, 'voucher_header_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->breakSettlements()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'break Settlements',
                'count' => $this->breakSettlements()->count(),
                'ids' => $this->breakSettlements()->pluck('amount')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Voucher Header')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }


}
