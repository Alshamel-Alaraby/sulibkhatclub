<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\BoardsRent\Entities\Panel;
use Spatie\Activitylog\LogOptions;

class Depertment extends Model
{
    use HasFactory,LogTrait  ;

    protected $table = 'general_departments';

    public function Panels()
    {
        return $this->hasMany(Panel::class,'department_id');
    }

    public function hasChildren()
    {
        $Children = $this->Panels()->count() > 0;
        return $Children;
    }



    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Depertment')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
