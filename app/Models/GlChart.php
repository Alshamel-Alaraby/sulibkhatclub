<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogTrait;
use Spatie\Activitylog\LogOptions;

class GlChart extends Model
{
    use HasFactory, LogTrait;
    
    protected $table = 'gl_chart';

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if(!$model->parent_id){
                $model->parent_id = $model->id;
                $model->save();
            }
            
        });

    }


    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',
                'account_number',
                'parent_id',
            )
            ->with([
                'parent:id,name,name_e,account_number',
            ]);
    }

    // relations

    public function parent()
    {
        return $this->belongsTo(\App\Models\GlChart::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(\App\Models\GlChart::class, 'parent_id');
    }

    public function revenueItems()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstRevenueItem::class, 'chart_id');
    }

    public function expenseItems()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstExpenseItem::class, 'chart_id');
    }

    public function buildingAccruedRevenues()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'accrued_revenues_account_id');

    }

    public function buildingAdvanceRevenues()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'advance_revenues_account_id');
    }

    public function buildingDiscounts()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'discounts_account_id');
    }

    public function buildingCash()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'cash_account_id');
    }

    public function buildingKnet()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'knet_account_id');
    }

    public function buildingInsurance()
    {
        return $this->hasMany(\Modules\RealEstate\Entities\RlstBuilding::class, 'insurance_account_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->children()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'children',
                'count' => $this->children()->count(),
                'ids' => $this->children()->pluck('id')->toArray(),
            ];
        }

        if ($this->revenueItems()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'revenueItems',
                'count' => $this->revenueItems()->count(),
                'ids' => $this->revenueItems()->pluck('rlst_revenues_items.id')->toArray(),
            ];
        }

        if ($this->expenseItems()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'expenseItems',
                'count' => $this->expenseItems()->count(),
                'ids' => $this->expenseItems()->pluck('rlst_expenses_items.id')->toArray(),
            ];
        }

        if ($this->buildingAccruedRevenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingAccruedRevenues',
                'count' => $this->buildingAccruedRevenues()->count(),
                'ids' => $this->buildingAccruedRevenues()->pluck('rlst_buildings.id')->toArray(),
            ];
        }


        if ($this->buildingAdvanceRevenues()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingAdvanceRevenues',
                'count' => $this->buildingAdvanceRevenues()->count(),
                'ids' => $this->buildingAdvanceRevenues()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingDiscounts()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingDiscounts',
                'count' => $this->buildingDiscounts()->count(),
                'ids' => $this->buildingDiscounts()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingCash()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingCash',
                'count' => $this->buildingCash()->count(),
                'ids' => $this->buildingCash()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingKnet()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingKnet',
                'count' => $this->buildingKnet()->count(),
                'ids' => $this->buildingKnet()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        if ($this->buildingInsurance()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'buildingInsurance',
                'count' => $this->buildingInsurance()->count(),
                'ids' => $this->buildingInsurance()->pluck('rlst_buildings.id')->toArray(),
            ];
        }

        return $relationsWithChildren;

    } // hasChildren()



    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('General Account')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

}
