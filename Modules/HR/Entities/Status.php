<?php

namespace Modules\HR\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class Status extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'name_e',
    ];
    protected $table = 'hr_statuses';

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Status')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    public function onlineRequests()
    {
        return $this->hasMany(OnlineRequest::class, 'status_id');
    }

    public function requests()
    {
        return $this->hasMany(Request::class, 'request_status_id');
    }


    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->onlineRequests()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'online Requests',
                'count' => $this->onlineRequests()->count(),
                'ids' => $this->onlineRequests()->pluck('id')->toArray(),
            ];
        }
        if ($this->requests()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'Requests',
                'count' => $this->requests()->count(),
                'ids' => $this->requests()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }

}
