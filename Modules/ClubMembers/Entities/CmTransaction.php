<?php

namespace Modules\ClubMembers\Entities;

use App\Models\Branch;
use App\Models\Document;
use App\Models\Serial;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Modules\ClubMembers\Transformers\CmTransactionResource;


use Spatie\Activitylog\LogOptions;

class CmTransaction extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];
    protected $table = 'cm_transactions';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
    }
    public function member()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmMember::class, 'cm_member_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(\Modules\ClubMembers\Entities\CmSponser::class, 'sponsor_id');
    }

    public function memberRequest()
    {
        return $this->belongsTo(CmMemberRequest::class, 'member_request_id');
    }

    public function scopeFilter($query, $request)
    {
        $query->where(function ($q) use ($request) {
            if ($request->search && $request->columns) {
                $q->orWhereHas('memberRequest', function ($query) use ($request) {
                    $query->where('full_name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->key && $request->value) {
                $q->where($request->key, $request->value);
            }
        });

        // Debug the SQL
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('CmTransaction')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            Cache::put('transaction_' . $transaction->id . '_true', collect(new CmTransactionResource($transaction))->only(['id', 'document_no', 'year', 'amount', 'serial', 'date']), 3600);
        });

        static::updated(function ($transaction) {
            Cache::forget('transaction_' . $transaction->id . '_true');

            Cache::put('transaction_' . $transaction->id . '_true', collect(new CmTransactionResource($transaction))->only(['id', 'document_no', 'year', 'amount', 'serial', 'date']), 3600);
        });

        static::deleted(function ($transaction) {
            Cache::forget('transaction_' . $transaction->id . '_true');
        });
    }
}
