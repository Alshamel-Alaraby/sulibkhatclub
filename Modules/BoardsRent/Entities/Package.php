<?php

namespace Modules\BoardsRent\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Package extends Model
{
    use HasFactory, LogTrait;

    protected $fillable = [
        'name',
        'name_e',
        'code',
        'price',
    ];
    protected $table = "boards_rent_packages";

    // relations

    public function orderDetails()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetail::class);
    }

    public function panels()
    {
        return $this->belongsToMany(\Modules\BoardsRent\Entities\Panel::class, 'boards_rent_package_panel', 'package_id', 'panel_id');
    }

    public function hasChildren()
    {
        return $this->orderDetails()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Package')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {


            if ($request->has('date')) {
                $q->whereDate('date', $request->date);
            }

            if ($request->search && $request->columns) {
                foreach ($request->columns as $column) {
                    if (strpos($column, ".") > 0) {
                        $column = explode(".", $column);
                        $q->orWhereRelation($column[0], $column[1], 'like', '%' . $request->search . '%');
                        // $q->orWhereHas($column[0], function ($q) use ($column, $request) {
                        //     $q->where($column[1], 'like', '%' . $request->search . '%');
                        // });
                    } else {
                        $q->orWhere($column, 'like', '%' . $request->search . '%');
                    }
                }
            }

            if (request()->header('company-id')) {
                if (in_array('company_id', $this->fillable)) {
                    $q->where('company_id', request()->header('company-id'));
                }
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }

        });
    }
}
