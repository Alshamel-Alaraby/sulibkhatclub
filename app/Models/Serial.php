<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\RealEstate\Entities\RlstInvoice;
use Spatie\Activitylog\LogOptions;

class Serial extends Model
{
    use HasFactory, LogTrait;
    protected $table = "general_serials";

    protected $fillable = [
        'start_no',
        'perfix',
        'suffix',
        'restart_period',
        'company_id',
        'document_id',
        'is_default',
        "branch_id",
    ];
    protected $casts = [
        'is_default' => 'App\Enums\IsDefault',
    ];
    protected $appends = [
        'serial_number',
        'has_child',
    ];

    public function orders()
    {
        return $this->hasMany(\Modules\BoardsRent\Entities\Order::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoices()
    {
        return $this->hasMany(RlstInvoice::class,'serial_id');
    }

    public function hasChildren()
    {
        return $this->orders()->count() > 0 || $this->invoices()->count() > 0;
    }

    public function getHasChildAttribute(){
        if ($this->invoices()->count() > 0){
            return 1;
        }
        return  0;
    }



    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Serial')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
