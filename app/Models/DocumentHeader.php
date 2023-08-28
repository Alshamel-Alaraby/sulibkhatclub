<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BoardsRent\Entities\SellMethod;
use Modules\BoardsRent\Entities\Task;

class DocumentHeader extends Model
{
    use HasFactory, SoftDeletes,LogTrait;

    protected $table = 'general_document_headers';

    protected $guarded = ['id'];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Document Header')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function documentStatus()
    {
        return $this->belongsTo(DocumentStatuse::class,'document_status_id');
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function relatedDocument()
    {
        return $this->belongsTo(Document::class,'related_document_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class,'document_id');
    }

    public function sellMethod()
    {
        return $this->belongsTo(SellMethod::class,'sell_method_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function customer()
    {
        return $this->belongsTo(GeneralCustomer::class,'customer_id');
    }

    public function task()
    {
        return $this->belongsTo(\App\Models\Task::class,'task_id');
    }
    public function externalSalesmen()
    {
        return $this->belongsTo(ExternalSalesmen::class,'external_salesmen_id');
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class,'serial_id');
    }

    public function documentNumber()
    {
        return $this->belongsTo(DocumentHeader::class,'related_document_number');
    }

    public function documentHeaderDetails(){
        return $this->hasMany(DocumentHeaderDetail::class,'document_header_id');
    }


    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function company()
    {
        return $this->belongsTo(GeneralCustomer::class,'company_id');
    }

    public function attendants()
    {
        return $this->belongstoMany(Attendant::class,'general_attendant_document_headers','document_header_id','attendant_id');
    }
    public function attendantsDocument()
    {
        return $this->belongstoMany(Attendant::class,'general_attendant_document_headers','document_header_id','attendant_id');
    }

}
