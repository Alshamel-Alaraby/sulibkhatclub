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
        // in the rlst_unit_services table.

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

        $requestData = request()->input(); // Get all request data

        $unitId = $requestData['unit_id'];

        $unitServicePairs = [];

        foreach($requestData['details'] as  $item){
            $unitServicePairs[] = $unitId . '-' . $item['service_id'].'-' . $item['from_date'];
        }
        $uniquePairsInRequest = count(array_unique($unitServicePairs)) === count($unitServicePairs);

        if(!$uniquePairsInRequest){
            return false;
        }


        // Check if each unit_id and its corresponding service_id is unique in the rlst_unit_services table
        if(!$this->id)
        foreach($requestData['details'] as  $item){
            $count = RlstUnitService::where('unit_id', $unitId)
                ->where('service_id', $item['service_id'])
                ->where('from_date', $item['from_date'])
                ->count();

            if ($count > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The combination of unit_id and service_id is not unique either among the request data or the database.';
    }
}
