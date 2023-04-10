<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory, LogTrait;

    protected $table = 'general_categories';

    protected $fillable = [
        'name',
        'name_e',

    ];

    public function orderDetails()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\OrderDetail::class);
    }

    public function hasChildren()
    {
        return
        $this->orderDetails()->count() > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Category')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
