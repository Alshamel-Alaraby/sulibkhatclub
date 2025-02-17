<?php

namespace App\Models;

use App\Models\Employee;
use App\Traits\LogTrait;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\HMS\Entities\HMSDoctor;
use Modules\HMS\Entities\HMSPatient;
use Modules\PointOfSale\Entities\Product;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    protected $table = "general_users";
    use HasApiTokens, HasFactory, Notifiable, MediaTrait, LogsActivity, CausesActivity, LogTrait,HasRoles;

    protected $fillable = [
        'name',
        'name_e',
        'email',
        'is_active',
        'password',
        "employee_id",
        "type",
        "company_id",
    ];


    public function scopeData($query)
    {
        return $query
            ->select('id','name', 'name_e', 'email', 'is_active', 'password', "employee_id", "type")
            ->with(['employee:id,name,name_e','roles','media']);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'is_active' => '\App\Enums\IsActive',
    ];

    protected function password(): Attribute
    {
        return Attribute::make(
            set:fn($value) => bcrypt($value),
        );
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function doctor()
    {
        return $this->hasOne(HMSDoctor::class,'user_id');
    }

    public function patient()
    {
        return $this->hasOne(HMSPatient::class,'user_id');
    }

    public function roleUsers()
    {
        return $this->hasMany(RoleUser::class, 'user_id');
    }

    public function taskLogs()
    {
        return $this->hasMany(TaskLog::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->doctor()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'doctor',
                'count' => $this->doctor()->count(),
                'ids' => $this->doctor()->pluck('name')->toArray(),
            ];
        }
        if ($this->products()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'products',
                'count' => $this->products()->count(),
                'ids' => $this->products()->pluck('title')->toArray(),
            ];
        }
        if ($this->patient()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'patient',
                'count' => $this->patient()->count(),
                'ids' => $this->patient()->pluck('name')->toArray(),
            ];
        }
        if ($this->taskLogs()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'task Logs',
                'count' => $this->taskLogs()->count(),
                'ids' => $this->taskLogs()->pluck('action')->toArray(),
            ];
        }
        if ($this->roleUsers()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'role Users',
                'count' => $this->roleUsers()->count(),
                'ids' => $this->roleUsers()->pluck('id')->toArray(),
            ];
        }

        return $relationsWithChildren;
    }



    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('User')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.'.$this->id;
    }


    // scopes
    public function scopeFilter($query, $request)
    {
        return $query->where(function ($q) use ($request) {

            // $q->whereNull('type');

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
