<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\BoardsRent\Entities\Package;

class DocumentHeaderDetail extends Model
{
    use HasFactory, SoftDeletes , LogTrait;

    protected $table = 'general_document_header_details';

    protected $guarded = ['id'];

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Document Header Detail')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function documentHeader()
    {
        return $this->belongsTo(DocumentHeader::class,'document_header_id');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class,'governorate_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class,'package_id');
    }

    public function itemBreakDowns()
    {
        return $this->hasMany(ItemBreakDown::class,'document_header_detail_id');
    }

}
