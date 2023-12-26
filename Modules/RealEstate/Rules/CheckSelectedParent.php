<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\RealEstate\Entities\RlstEvacuationType;

class CheckSelectedParent implements Rule
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

             // Log the $value and $attribute
             //Log::info('Validation Attribute: ' . $attribute);
             //Log::info('Validation Value: ' . json_encode($value));

            $requestData = request()->input(); // Get all request data


            $count = RlstEvacuationType::where('id', '!=', $this->id)
                    ->where('id', $value)
                    ->where('parent_id', '!=', $value)
                    ->count();

            return $count == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'grandchild evacuation type is not allowed.';
    }
}
