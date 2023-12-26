<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageType extends Model
{
    use HasFactory, LogTrait;
    protected $table = 'general_message_types';

    protected $guarded = ['id'];

    public function scopeData($query)
    {
        return $query
            ->select(
                'id',
                'name',
                'name_e',

            );
    }

    public function GeneralMessageRequests()
    {
        return $this->hasMany(GeneralMessageRequest::class, 'message_type_id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'message_type_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->GeneralMessageRequests()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'General Message Requests',
                'count' => $this->GeneralMessageRequests()->count(),
                'ids' => $this->GeneralMessageRequests()->pluck('message_date')->toArray(),
            ];
        }
        if ($this->messages()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'messages',
                'count' => $this->messages()->count(),
                'ids' => $this->messages()->pluck('content')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        $user = @auth()->user()->id ?: "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Message Type')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
