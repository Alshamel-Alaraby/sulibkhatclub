<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RecievablePayable\Entities\RpPaymentPlanInstallment;

class Document extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_documents';

    protected $guarded = ['id'];
    protected $casts = ["attributes" => "json", "contusion" => "\App\Enums\BooleanStatus"];

    public function payment_plan_installments()
    {
        return $this->hasMany(RpPaymentPlanInstallment::class, 'doc_type_id');
    }

    public function orders ()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class, 'doc_type_id');
    }

    public function documentRelateds()
    {
        return $this->belongsToMany(Document::class, 'general_document_related', 'document_id', 'document_related_id','id','id');
    }


    public function hasChildren()
    {
        return $this->orders->count() > 0 ||
        $this->payment_plan_installments->count() > 0;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Avenue')
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
