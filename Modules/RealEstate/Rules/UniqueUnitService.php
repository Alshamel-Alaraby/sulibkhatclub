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
        // Check if the combination of unit_ids and service_ids is unique
        // in the rlst_unit_services table.

         // Log the $value and $attribute
         //Log::info('Validation Attribute: ' . $attribute);
         //Log::info('Validation Value: ' . json_encode($value));

        $requestData = request()->input(); // Get all request data

        $unitIds = $requestData['unit_ids'];
        $serviceIds = $requestData['service_ids'];

        $unitServicePairs = [];

        $i = 0;
        foreach($unitIds as $unitIdx => $unitId){
            foreach($serviceIds as $serviceIdx => $serviceId){
                $unitServicePairs[$i] = $unitId . '-' . $serviceId;
            }
        }
        $uniquePairsInRequest = count(array_unique($unitServicePairs)) === count($unitServicePairs);

        if(!$uniquePairsInRequest){
            return false;
        }


        // Check if each unit_id and its corresponding service_id is unique in the rlst_unit_services table
        foreach ($unitIds as $index => $unitId) {
            $serviceId = $serviceIds[$index];

            $count = RlstUnitService::where('id', '!=', $this->id)
                ->where('unit_id', $unitId)
                ->where('service_id', $serviceId)
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
