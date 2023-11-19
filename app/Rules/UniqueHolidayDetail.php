<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\HR\Entities\EmployeePublicHolidayDetail;

class UniqueHolidayDetail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $employeePublicHolidayHeaderId;
    private $employeeIds;

    public function __construct($employeePublicHolidayHeaderId, $employeeIds)
    {
        $this->employeePublicHolidayHeaderId = $employeePublicHolidayHeaderId;
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
        return !EmployeePublicHolidayDetail::where('employee_public_holiday_header_id', $this->employeePublicHolidayHeaderId)
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
        return 'Can\'t choose employee_public_holiday_header_id with same employee_id.';
    }
}
