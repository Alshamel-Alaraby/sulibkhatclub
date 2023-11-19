<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\HR\Entities\EmployeeFingerprintDetail;

class UniqueEmployeeFingerprintDetail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $employeeFingerprintHeaderId;
    private $employeeIds;

    public function __construct($employeeFingerprintHeaderId, $employeeIds)
    {
        $this->employeeFingerprintHeaderId = $employeeFingerprintHeaderId;
        $this->employeeIds = $employeeIds;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !EmployeeFingerprintDetail::where('employee_fingerprint_header_id', $this->employeeFingerprintHeaderId)
        ->whereIn('employee_id', $this->employeeIds)
        ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Can\'t choose employee_fingerprint_header_id with same employee_id.';
    }
}
