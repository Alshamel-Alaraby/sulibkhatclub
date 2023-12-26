<?php

namespace Modules\RealEstate\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogTrait;
use App\Models\GlChart;


class RlstRevenueItem extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'rlst_revenues_items';
    protected $guarded = ['id'];


    public function scopeData($query)
    {
        return $query->select('id', 'name', 'name_e','chart_id','type')
            ->with([
                'chart:id,name,name_e,account_number'
            ]);
            
    }

    // relations

    public function chart()
    {
        return $this->belongsTo(GlChart::class, 'chart_id');

    }


    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('RevenueItem')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
