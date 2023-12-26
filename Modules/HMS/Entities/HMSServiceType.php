<?php

namespace Modules\HMS\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSServiceType extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ["id"];

    public function specialty(){
        return $this->belongsTo(HMSSpecialty::class,'specialty_id');
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            if ($request->search && $request->columns) {
                foreach ($request->columns as $column) {
                    if($column = 'price')
                        $q->orWhere($column, $request->search);
                    else
                        $q->orWhere($column, 'like', '%'.$request->search . '%');

                }
            }

            if (request()->header('company-id')) {
                if (in_array('company_id', $this->fillable)) {
                    $q->where('company_id', request()->header('company-id'));
                }
            }

        });
    }

//    public function getActivitylogOptions(): LogOptions
//    {
//        $user = auth()->user()->id ?? "system";
//
//        return \Spatie\Activitylog\LogOptions::defaults()
//            ->logAll()
//            ->useLogName('ServiceType')
//            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
//    }
}
