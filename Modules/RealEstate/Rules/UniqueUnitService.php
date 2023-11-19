<?php

namespace Modules\RealEstate\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\RealEstate\Entities\RlstUnitService;
use Illuminate\Support\Facades\Log;



class UniqueUnitService implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private $id;

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
        // Check if the combination of unit_id and service_id is unique
        // in the rlst_unit_services table.

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

         $requestData = request()->input(); // Get all request data

         //print_r("unitId =", $requestData['unit-service'][0]['unit_id']);

         $unitId = $requestData['unit-service'][0]['unit_id'];


        $count = RlstUnitService::where('id', '!=', $this->id)
            ->where('service_id', $value)
            ->where('unit_id', $unitId)
            ->count();

        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The combination of unit_id and service_id is not unique.';
    }
}
