<?php

namespace Modules\HR\Entities;

use App\Models\Employee;
use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

class EmployeeFingerprintHeaders extends Model
{
    use HasFactory, LogTrait;

    protected $guarded = ['id'];

    protected $table = 'hr_employee_fingerprint_headers';

    public function employees()
    {
        return $this->BelongsToMany(Employee::class, 'hr_employee_fingerprint_details', 'employee_fingerprint_header_id', 'employee_id');
    }

    public function employeeFingerprintDetails()
    {
        return $this->hasMany(EmployeeFingerprintDetail::class, 'employee_fingerprint_header_id');
    }

    public function hasChildren()
    {
        $relationsWithChildren = [];

        if ($this->employeeFingerprintDetails()->count() > 0) {
            $relationsWithChildren[] = [
                'relation' => 'employeeFingerprintDetails',
                'count' => $this->employeeFingerprintDetails()->count(),
                'ids' => $this->employeeFingerprintDetails()->pluck('id')->toArray(),
            ];
        }


        return $relationsWithChildren;
    }


    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Employee Fingerprint Headers')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
