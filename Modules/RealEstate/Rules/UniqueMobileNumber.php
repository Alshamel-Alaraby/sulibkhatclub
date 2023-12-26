<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueMobileNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
        // Check if the combination of mobile_code and number is unique
        // in the rlst_tenants table.

        $requestData = request()->input(); // Get all request data

        $mobileCode = $requestData['mobile_code'];
        $mobile = $requestData['mobile'];

        $uniqueMobileInRequest = DB::table('rlst_tenants')
            ->where('id', '!=', $this->id)
            ->where('mobile_code', $mobileCode)
            ->where('mobile', $mobile)
            ->count();

        return $uniqueMobileInRequest === 0;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The mobile number is already taken.';
    }
}
