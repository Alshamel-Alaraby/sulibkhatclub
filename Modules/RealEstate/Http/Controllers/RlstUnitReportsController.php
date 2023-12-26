<?php

namespace Modules\RealEstate\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Entities\RlstUnitStatus;
use Modules\RealEstate\Entities\RlstWallet;
use Modules\RealEstate\Entities\RlstOwner;
use Modules\RealEstate\Entities\RlstBuilding;
use Modules\RealEstate\Entities\RlstPolicy;
use Modules\RealEstate\Entities\RlstBuildingType;
use Modules\RealEstate\Entities\RlstUnitType;
use Modules\RealEstate\Entities\RlstView;
use Modules\RealEstate\Entities\RlstFinishing;
use Modules\RealEstate\Entities\RlstBuildingWallet;
use Modules\RealEstate\Entities\RlstBuildingPolicy;
use Modules\RealEstate\Entities\RlstWalletOwner;
use Modules\RealEstate\Transformers\RlstUnitResource;
use Modules\RealEstate\Transformers\RlstUnitStatusResource;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use App\Models\Avenue;
use App\Models\Street;
use App\Traits\LogTrait;
use Illuminate\Support\Facades\DB;
use Log;
use Artisan;
use File;

class RlstUnitReportsController extends Controller
{

    

    private $walletsList;
    private $newWalletsList;
    private $nullWalletsList;

    private $ownersList;
    private $newOwnersList;
    private $nullOwnersList;

    private $buildingsList;
    private $newBuildingsList;
    private $nullBuildingsList;

    private $policiesList;
    private $newPoliciesList;
    private $nullPoliciesList;

    private $countriesList;
    private $newCountriesList;
    private $nullCountriesList;

    private $governoratesList;
    private $newGovernoratesList;
    private $nullGovernoratesList;

    private $citiesList;
    private $newCitiesList;
    private $nullCitiesList;

    private $avenuesList;
    private $newAvenuesList;
    private $nullAvenuesList;

    private $streetsList;
    private $newStreetsList;
    private $nullStreetsList;

    private $buildingTypesList;
    private $newBuildingTypesList;
    private $nullBuildingTypesList;

    private $unitTypesList;
    private $newUnitTypesList;
    private $nullUnitTypesList;

    private $unitStatusesList;
    private $newUnitStatusesList;
    private $nullUnitStatusesList;

    private $viewsList;
    private $newViewsList;
    private $nullViewsList;

    private $finishingsList;
    private $newFinishingsList;
    private $nullFinishingsList;

    private $unitsList;
    private $newUnitsList;
    private $nullUnitsList;

    private $unitAreaFrom;
    private $unitAreaTo;

    private $unitRoomsFrom;
    private $unitRoomsTo;

    private $unitBathroomsFrom;
    private $unitBathroomsTo;

    private $newData;

    private $tableBuildingWallet_Building;
    private $tableBuildingWallet_Wallet;

    private $tableBuildingPolicy_Building;
    private $tableBuildingPolicy_Policy;

    private $tableWalletOwner_Wallet;
    private $tableWalletOwner_Owner;

    public function __construct(Request $request)
    {
        File::delete(storage_path('logs/custom.log'));
        //Log::channel('custom')->info('entered __construct');

        $this->request = $request;
        
        $this->walletsList = RlstWallet::pluck('id')->toArray();
        $this->newWalletsList = $this->walletsList;
        $this->nullWalletsList = false;

        //Log::channel('custom')->info('all walletsList = '. json_encode($this->walletsList));
        //Log::channel('custom')->info('all newWalletsList = '. json_encode($this->newWalletsList));

        $this->ownersList = RlstOwner::pluck('id')->toArray();
        $this->newOwnersList = $this->ownersList;
        $this->nullOwnersList = false;

        //Log::channel('custom')->info('all ownersList = '. json_encode($this->ownersList));
        //Log::channel('custom')->info('all newOwnersList = '. json_encode($this->newOwnersList));

        $this->buildingsList = RlstBuilding::pluck('id')->toArray();
        $this->newBuildingsList = $this->buildingsList;
        $this->nullBuildingsList = false;

        //Log::channel('custom')->info('all buildingsList = '. json_encode($this->buildingsList));
        //Log::channel('custom')->info('all newBuildingsList = '. json_encode($this->newBuildingsList));

        $this->policiesList = RlstPolicy::pluck('id')->toArray();
        $this->newPoliciesList = $this->policiesList;
        $this->nullPoliciesList = false;

        //Log::channel('custom')->info('all policiesList = '. json_encode($this->policiesList));
        //Log::channel('custom')->info('all newPoliciesList = '. json_encode($this->newPoliciesList));

        $this->countriesList = Country::pluck('id')->toArray();
        $this->newCountriesList = $this->countriesList;
        $this->nullCountriesList = false;

        //Log::channel('custom')->info('all countriesList = '. json_encode($this->countriesList));
        //Log::channel('custom')->info('all newCountriesList = '. json_encode($this->newCountriesList));

        $this->governoratesList = Governorate::pluck('id')->toArray();
        $this->newGovernoratesList = $this->governoratesList;
        $this->nullGovernoratesList = false;

        //Log::channel('custom')->info('all governoratesList = '. json_encode($this->governoratesList));
        //Log::channel('custom')->info('all newGovernoratesList = '. json_encode($this->newGovernoratesList));

        $this->citiesList = City::pluck('id')->toArray();
        $this->newCitiesList = $this->citiesList;
        $this->nullCitiesList = false;

        //Log::channel('custom')->info('all citiesList = '. json_encode($this->citiesList));
        //Log::channel('custom')->info('all newCitiesList = '. json_encode($this->newCitiesList));

        $this->avenuesList = Avenue::pluck('id')->toArray();
        $this->newAvenuesList = $this->avenuesList;
        $this->nullAvenuesList = false;

        //Log::channel('custom')->info('all avenuesList = '. json_encode($this->avenuesList));
        //Log::channel('custom')->info('all newAvenuesList = '. json_encode($this->newAvenuesList));

        $this->streetsList = Street::pluck('id')->toArray();
        $this->newStreetsList = $this->streetsList;
        $this->nullStreetsList = false;

        //Log::channel('custom')->info('all streetsList = '. json_encode($this->streetsList));
        //Log::channel('custom')->info('all newStreetsList = '. json_encode($this->newStreetsList));

        $this->buildingTypesList = RlstBuildingType::pluck('id')->toArray();
        $this->newBuildingTypesList = $this->buildingTypesList;
        $this->nullBuildingTypesList = false;

        //Log::channel('custom')->info('all buildingTypesList = '. json_encode($this->buildingTypesList));
        //Log::channel('custom')->info('all newBuildingTypesList = '. json_encode($this->newBuildingTypesList));

        $this->unitTypesList = RlstUnitType::pluck('id')->toArray();
        $this->newUnitTypesList = $this->unitTypesList;
        $this->nullUnitTypesList = false;

        //Log::channel('custom')->info('all unitTypesList = '. json_encode($this->unitTypesList));
        //Log::channel('custom')->info('all newUnitTypesList = '. json_encode($this->newUnitTypesList));

        $this->unitStatusesList = RlstUnitStatus::pluck('id')->toArray();
        $this->newUnitStatusesList = $this->unitStatusesList;
        $this->nullUnitStatusesList = false;

        //Log::channel('custom')->info('all unitStatusesList = '. json_encode($this->unitStatusesList));
        //Log::channel('custom')->info('all newUnitStatusesList = '. json_encode($this->newUnitStatusesList));

        $this->viewsList = RlstView::pluck('id')->toArray();
        $this->newViewsList = $this->viewsList;
        $this->nullViewsList = false;

        //Log::channel('custom')->info('all viewsList = '. json_encode($this->viewsList));
        //Log::channel('custom')->info('all newViewsList = '. json_encode($this->newViewsList));

        $this->finishingsList = RlstFinishing::pluck('id')->toArray();
        $this->newFinishingsList = $this->finishingsList;
        $this->nullFinishingsList = false;

        //Log::channel('custom')->info('all finishingsList = '. json_encode($this->finishingsList));
        //Log::channel('custom')->info('all newFinishingsList = '. json_encode($this->newFinishingsList));

        $this->unitsList = RlstUnit::pluck('id')->toArray();
        $this->newUnitsList = $this->unitsList;
        $this->nullUnitsList = false;

        //Log::channel('custom')->info('all unitsList = '. json_encode($this->unitsList));
        //Log::channel('custom')->info('all newUnitsList = '. json_encode($this->newUnitsList));

        $this->unitAreaFrom = RlstUnit::min('unit_area');
        $this->unitAreaTo = RlstUnit::max('unit_area');

        //Log::channel('custom')->info('min unitAreaFrom = '. $this->unitAreaFrom);
        //Log::channel('custom')->info('max unitAreaTo = '. $this->unitAreaTo);

        $this->unitRoomsFrom = RlstUnit::min('rooms');
        $this->unitRoomsTo = RlstUnit::max('rooms');

        //Log::channel('custom')->info('min unitRoomsFrom = '. $this->unitRoomsFrom);
        //Log::channel('custom')->info('max unitRoomsTo = '. $this->unitRoomsTo);

        $this->unitBathroomsFrom = RlstUnit::min('path');
        $this->unitBathroomsTo = RlstUnit::max('path');

        //Log::channel('custom')->info('min unitBathroomsFrom = '. $this->unitBathroomsFrom);
        //Log::channel('custom')->info('max unitBathroomsTo = '. $this->unitBathroomsTo);

    }


    
    public function prepareLists(Request $request)
    {
        /*
        Artisan::call("cache:clear");
        Artisan::call("config:clear");
        Artisan::call("config:cache");
        Artisan::call("view:clear");
        Artisan::call("route:clear");
        Artisan::call("route:cache");
        Artisan::call("optimize:clear");
        Artisan::call("optimize");
        Artisan::call("clear-compiled");
        */

        Artisan::call("db:seed --class=GeneralExcelFiltersTableSeeder");

        //Log::channel('custom')->info('entered getLists');
        
        // Dealing with requests

        $requestedUnitsList = RlstUnit::When($request->unit_statuses, function ($query, $unitStatusesList) {
            return $query->whereIn('unit_status_id', $unitStatusesList);
        })
        ->when($request->building_ids, function ($query, $buildingIds) {
            return $query->whereIn('building_id', $buildingIds);
        })
        ->when($request->unit_area_from, function ($query, $unitAreaFrom) {
            return $query->where('unit_area', '>=', $unitAreaFrom);
        })
        ->when($request->unit_area_to, function ($query, $unitAreaTo) {
            return $query->where('unit_area', '<=', $unitAreaTo);
        })
        ->when($request->unit_rooms_from, function ($query, $unitRoomsFrom) {
            return $query->where('unit_rooms', '>=', $unitRoomsFrom);
        })
        ->when($request->unit_rooms_to, function ($query, $unitRoomsTo) {
            return $query->where('unit_rooms', '<=', $unitRoomsTo);
        })
        ->when($request->unit_bathrooms_from, function ($query, $unitBathroomsFrom) {
            return $query->where('unit_bathrooms', '>=', $unitBathroomsFrom);
        })
        ->when($request->unit_bathrooms_to, function ($query, $unitBathroomsTo) {
            return $query->where('unit_bathrooms', '<=', $unitBathroomsTo);
        })
        ->when($request->unit_finishing_ids, function ($query, $unitFinishingIds) {
            return $query->whereIn('unit_finishing_id', $unitFinishingIds);
        })
        ->when($request->unit_view_ids, function ($query, $unitViewIds) {
            return $query->whereIn('unit_view_id', $unitViewIds);
        })
        ->when($request->unit_type_ids, function ($query, $unitTypeIds) {
            return $query->whereIn('unit_ty', $unitTypeIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start unitsList = '. json_encode($this->unitsList));
        if($this->customEmpty($this->unitsList))
            $this->nullUnitsList = true;
            



        $requestedBuildingsList = RlstBuilding::when($request->building_ids, function ($query, $buildingIds) {
            return $query->whereIn('id', $buildingIds);
        })
        ->when($request->street_ids, function($query, $streetIds) {
            return $query->whereIn('street_id', $streetIds);
        })
        ->when($request->building_type_ids, function($query, $buildingTypeIds) {
            return $query->whereIn('building_type_id', $buildingTypeIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start buildingsList = '. json_encode($this->buildingsList));
        if($this->customEmpty($requestedBuildingsList))
            $this->nullBuildingsList = true;



        $requestedWalletsList = RlstWallet::when($request->wallet_ids, function ($query, $walletIds) {
            return $query->whereIn('id', $walletIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start walletsList = '. json_encode($this->walletsList));
        if($this->customEmpty($requestedWalletsList))
            $this->nullWalletsList = true;



        $requestedOwnersList = RlstOwner::when($request->owner_ids, function ($query, $ownerIds) {
            return $query->whereIn('id', $ownerIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start ownersList = '. json_encode($this->ownersList));
        if($this->customEmpty($requestedOwnersList))
            $this->nullOwnersList = true;



        $requestedPoliciesList = RlstPolicy::when($request->policy_ids, function ($query, $policyIds) {
            return $query->whereIn('id', $policyIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start policiesList = '. json_encode($this->policiesList));
        if($this->customEmpty($requestedPoliciesList))
            $this->nullPoliciesList = true;



        $requestedCountriesList = Country::when($request->country_ids, function ($query, $countryIds) {
            return $query->whereIn('id', $countryIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start countriesList = '. json_encode($this->countriesList));
        if($this->customEmpty($requestedCountriesList))
            $this->nullCountriesList = true;


        $requestedGovernoratesList = Governorate::when($request->governorate_ids, function ($query, $governorateIds) {
            return $query->whereIn('id', $governorateIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start governoratesList = '. json_encode($this->governoratesList));
        if($this->customEmpty($requestedGovernoratesList))
            $this->nullGovernoratesList = true;


        $requestedCitiesList = City::when($request->city_ids, function ($query, $cityIds) {
            return $query->whereIn('id', $cityIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start citiesList = '. json_encode($this->citiesList));
        if($this->customEmpty($requestedCitiesList))
            $this->nullCitiesList = true;


        $requestedAvenuesList = Avenue::when($request->avenue_ids, function ($query, $avenueIds) {
            return $query->whereIn('id', $avenueIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start avenuesList = '. json_encode($this->avenuesList));
        if($this->customEmpty($requestedAvenuesList))
            $this->nullAvenuesList = true;
        

        $requestedStreetsList = Street::when($request->street_ids, function ($query, $streetIds) {
            return $query->whereIn('id', $streetIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start streetsList = '. json_encode($this->streetsList));
        if($this->customEmpty($requestedStreetsList))
            $this->nullStreetsList = true;


        $requestedBuildingTypesList = RlstBuildingType::when($request->building_type_ids, function ($query, $buildingTypeIds) {
            return $query->whereIn('id', $buildingTypeIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start buildingTypesList = '. json_encode($this->buildingTypesList));
        if($this->customEmpty($requestedBuildingTypesList))
            $this->nullBuildingTypesList = true;


        $requestedUnitTypesList = RlstUnitType::when($request->unit_type_ids, function ($query, $unitTypeIds) {
            return $query->whereIn('id', $unitTypeIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start unitTypesList = '. json_encode($this->unitTypesList));
        if($this->customEmpty($requestedUnitTypesList))
            $this->nullUnitTypesList = true;
        

        $requestedUnitStatusesList = RlstUnitStatus::when($request->unit_statuses, function ($query, $unitStatusesList) {
            return $query->whereIn('id', $unitStatusesList);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start unitStatusesList = '. json_encode($this->unitStatusesList));
        if($this->customEmpty($requestedUnitStatusesList))
            $this->nullUnitStatusesList = true;

        
        $requestedViewsList = RlstView::when($request->unit_view_ids, function ($query, $unitViewIds) {
            return $query->whereIn('id', $unitViewIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start viewsList = '. json_encode($this->viewsList));
        if($this->customEmpty($requestedViewsList))
            $this->nullViewsList = true;

        $requestedFinishingsList = RlstFinishing::when($request->unit_finishing_ids, function ($query, $unitFinishingIds) {
            return $query->whereIn('id', $unitFinishingIds);
        })
        ->pluck('id')->toArray();
        //Log::channel('custom')->info('start finishingsList = '. json_encode($this->finishingsList));
        if($this->customEmpty($requestedFinishingsList))
            $this->nullFinishingsList = true;




        if($this->checkDiff($this->unitTypesList, $requestedUnitTypesList))
            $this->updateUnitTypesList();
        
        if($this->checkDiff($this->unitStatusesList, $requestedUnitStatusesList))
            $this->updateUnitStatuses();

        if($this->checkDiff($this->viewsList, $requestedViewsList))
            $this->updateViewsList();

        if($this->checkDiff($this->finishingsList, $requestedFinishingsList))
            $this->updateFinishingsList();
        

        if($this->checkDiff($this->citiesList, $requestedCitiesList))
           $this->updateCitiesList();
         

        if($this->checkDiff($this->policiesList, $requestedPoliciesList))
            $this->updatePoliciesList();
        
            
        if($this->checkDiff($this->buildingsList, $requestedBuildingsList))
            $this->updateBuildingsList();


        if($this->checkDiff($this->ownersList, $requestedOwnersList))
            $this->updateOwnersList();


        if($this->checkDiff($this->walletsList, $requestedWalletsList))
            $this->updateWalletsList();

        
        if($this->checkDiff($this->buildingTypesList, $requestedBuildingTypesList))
            $this->updateBuildingTypesList();


        if($this->checkDiff($this->countriesList, $requestedCountriesList))     
             $this->updateCountriesList();

        if($this->checkDiff($this->governoratesList, $requestedGovernoratesList))        
             $this->updateGovernoratesList();

        if($this->checkDiff($this->avenuesList, $requestedAvenuesList))
             $this->updateAvenuesList();

        if($this->checkDiff($this->streetsList, $requestedStreetsList))
            $this->updateStreetsList();
            

        if($this->checkDiff($this->buildingsList, $requestedBuildingsList))
            $this->updateBuildingsList();
        
        if($this->checkDiff($this->unitsList, $requestedUnitsList))
            $this->updateUnitsList();
        
        

   
    } // public function prepareLists(Request $request)


    public function getLists(Request $request){

        //return $this->getColumnFKName($request->table_name, $request->column_name);  
        
        //return $this->getDirectlyDependentTablesWithFKs();

        $this->prepareLists($request);

        $models = [
            //'results' => RlstUnitResource::collection(RlstUnit::data()->whereIn('id', $this->unitsList)->get()),
            'building_ids' => RlstBuilding::whereIn('id', $this->buildingsList)->select('id','name','name_e')->get(),
            'wallet_ids' => RlstWallet::whereIn('id', $this->walletsList)->select('id','name','name_e')->get(),
            'owner_ids' => RlstOwner::whereIn('id', $this->ownersList)->select('id','name','name_e')->get(),
            'policy_ids' => RlstPolicy::whereIn('id', $this->policiesList)->select('id','name','name_e')->get(),
            'country_ids' => Country::whereIn('id', $this->countriesList)->select('id','name','name_e')->get(),
            'governorate_ids' => Governorate::whereIn('id', $this->governoratesList)->select('id','name','name_e')->get(),
            'city_ids' => City::whereIn('id', $this->citiesList)->select('id','name','name_e')->get(),
            'avenue_ids' => Avenue::whereIn('id', $this->avenuesList)->select('id','name','name_e')->get(),
            'street_ids' => Street::whereIn('id', $this->streetsList)->select('id','name','name_e')->get(),
            'building_type_ids' => RlstBuildingType::whereIn('id', $this->buildingTypesList)->select('id','name','name_e')->get(),
            'unit_type_ids' => RlstUnitType::whereIn('id', $this->unitTypesList)->select('id','name','name_e')->get(),
            'unit_status_ids' => RlstUnitStatus::data()->whereIn('id', $this->unitStatusesList)->select('id','name','name_e')->get(),
            'view_ids' => RlstView::whereIn('id', $this->viewsList)->select('id','name','name_e')->get(),
            'finishing_ids' => RlstFinishing::whereIn('id', $this->finishingsList)->select('id','name','name_e')->get(),
            'unit_area_from' => $this->unitAreaFrom,
            'unit_area_to' => $this->unitAreaTo,
            'unit_rooms_from' => $this->unitRoomsFrom,
            'unit_rooms_to' => $this->unitRoomsTo,
            'unit_bathrooms_from' => $this->unitBathroomsFrom,
            'unit_bathrooms_to' => $this->unitBathroomsTo,
            ];

            // switch to session when you test on the front 
            //session(['units_list' => $this->unitsList]);
            cachePut('units_list', $this->unitsList, 60*60*24); // 24 hours

        return responseJson(200, 'success', $models);
    

    } // public function getLists()

    public function getResults(Request $request)
    {
        // switch to session when you test on the front
        //$unitsList = session('units_list');
        $unitsList = cacheGet('units_list');


        $this->models = RlstUnit::data()->whereIn('id', $unitsList)->get();


        $models = ['results' => RlstUnitResource::collection($this->models)];

        cachePut('excel_models', $this->models, 60*60*24); // 24 hours

        return responseJson(200, 'success', $models); 

    }

    public function getExcelResults(Request $request)
    {
        $excelModels = cacheGet('excel_models');

        $filteredExcelModels =

        $excelModels->when($request->x_code, function ($query, $codesList) {
            return $query->whereIn('code', $codesList);
        })
        ->when($request->x_building_ids, function ($query, $buildingIds) {
            return $query->whereIn('building_id', $buildingIds);
        })
        ->when($request->x_unit_statuses, function ($query, $unitStatusesList) {
            return $query->whereIn('unit_status_id', $unitStatusesList);
        })
        ->when($request->x_description, function ($query, $description) {
            return $query->whereIn('description', $description);
        })
        ->when($request->x_description_e, function ($query, $description) {
            return $query->whereIn('description_e', $description);
        })
        ->when($request->x_name, function ($query, $name) {
            return $query->whereIn('name', $name);
        })
        ->when($request->x_name_e, function ($query, $name) {
            return $query->whereIn('name_e', $name);
        })        
        ->when($request->x_unit_area, function ($query, $unitAreaFrom) {
            return $query->where('unit_area', $unitAreaFrom);
        })
        ->when($request->x_unit_rooms, function ($query, $unitRoomsFrom) {
            return $query->where('rooms', $unitRoomsFrom);
        })
        ->when($request->x_unit_bathrooms, function ($query, $unitBathroomsFrom) {
            return $query->where('path', $unitBathroomsFrom);
        })
        ->when($request->x_unit_finishing_ids, function ($query, $unitFinishingIds) {
            return $query->whereIn('finishing', $unitFinishingIds);
        })
        ->when($request->x_unit_view_ids, function ($query, $unitViewIds) {
            return $query->whereIn('view', $unitViewIds);
        })
        ->when($request->x_floor, function ($query, $floor) {
            return $query->whereIn('floor', $floor);
        })
        ->when($request->x_unit_type_ids, function ($query, $unitTypeIds) {
            return $query->whereIn('unit_ty', $unitTypeIds);
        });
        

        $models = ['excel_results' => RlstUnitResource::collection($excelModels)];

        // update the cache
        cachePut('excel_models', $filteredExcelModels, 60*60*24); // 24 hours

        return responseJson(200, 'success', $models); 

    }

    public function getFilteredExcelResults(Request $request)
    {
        $excelModels = cacheGet('excel_models');

        $filtered = excelFilter($request->filter_id, $excelModels, $request->column_name, $request->value_1, $request->value_2);

        cachePut('excel_models', $filtered, 60*60*24); // 24 hours

        $models = ['excel_results' => RlstUnitResource::collection($filtered)];

        return responseJson(200, 'success', $models); 

    }

    // NEEDED functions


    public function updateUnitStatusesList()
    {   
            //Log::channel('custom')->info('entered updateUnitStatusesList');
    
            if($this->customEmpty($this->newUnitStatusesList)){
                $this->nullUnitStatusesList = true;
                $this->unitStatusesList = [];
                return;
            }

            $this->unitStatusesList = $this->getArraysOverlap($this->newUnitStatusesList,$this->unitStatusesList);

        // affects unitsList

            
            if(!$this->request->unit_statuses)
                $unitStatusesList = $this->unitStatusesList;
            else
                $unitStatusesList = $this->request->unit_statuses;

            //$this->newUnitsList = RlstUnit::whereIn('unit_status_id', $this->unitStatusesList)->pluck('id')->toArray();
            $this->newUnitsList = DB::table('rlst_units')->whereIn('unit_status_id', $unitStatusesList)->pluck('id')->toArray();

            //Log::channel('custom')->info('unitStatusesList = '. json_encode($this->unitStatusesList));
            //Log::channel('custom')->info('newUnitsList = '. json_encode($this->newUnitsList));


            if($this->checkDiff($this->unitsList, $this->newUnitsList))
                $this->updateUnitsList();   
    }

    public function updateViewsList()
    {   
            //Log::channel('custom')->info('entered updateViewsList');    

            if($this->customEmpty($this->newViewsList) || $this->nullViewsList){
                $this->nullViewsList = true;
                $this->viewsList = [];
                return;
            }

            $this->viewsList = $this->getArraysOverlap($this->newViewsList,$this->viewsList);

            if(!$this->request->unit_view_ids)
                $viewsList = $this->viewsList;
            else
                $viewsList = $this->request->unit_view_ids;
            
            // affects unitsList
            //$this->newUnitsList = RlstUnit::whereIn('view', $this->viewsList)->pluck('id')->toArray();
            $this->newUnitsList = DB::table('rlst_units')->whereIn('view', $viewsList)->pluck('id')->toArray();

            //Log::channel('custom')->info('viewsList = '. json_encode($this->viewsList));
            //Log::channel('custom')->info('newUnitsList = '. json_encode($this->newUnitsList));

            if($this->checkDiff($this->unitsList, $this->newUnitsList))
                $this->updateUnitsList();

    }

    public function updateFinishingsList()
    {   
        //Log::channel('custom')->info('entered updateFinishingsList');

        if($this->customEmpty($this->newFinishingsList) || $this->nullFinishingsList){
            $this->nullFinishingsList = true;
            $this->finishingsList = [];
            return;
        }
        
        $this->finishingsList = $this->getArraysOverlap($this->newFinishingsList,$this->finishingsList);
        
        if(!$this->request->unit_finishing_ids)
            $finishingsList = $this->finishingsList;
        else
            $finishingsList = $this->request->unit_finishing_ids;

        // affects unitsList
        //$this->newUnitsList = RlstUnit::whereIn('finishing', $this->finishingsList)->pluck('id')->toArray();
        $this->newUnitsList = DB::table('rlst_units')->whereIn('finishing', $finishingsList)->pluck('id')->toArray();

        //Log::channel('custom')->info('finishingsList = '. json_encode($this->finishingsList));
        //Log::channel('custom')->info('newUnitsList = '. json_encode($this->newUnitsList));


        if($this->checkDiff($this->unitsList, $this->newUnitsList))
            $this->updateUnitsList();

    }

    public function updateUnitTypesList()
    {   
        //Log::channel('custom')->info('entered updateUnitTypesList');

        if($this->customEmpty($this->newUnitTypesList) || $this->nullUnitTypesList){
            $this->nullUnitTypesList = true;
            $this->unitTypesList = [];
            return;
        }
        
        $this->unitTypesList = $this->getArraysOverlap($this->newUnitTypesList,$this->unitTypesList);

        if(!$this->request->unit_type_ids)
            $unitTypesList = $this->unitTypesList;
        else
            $unitTypesList = $this->request->unit_type_ids;
            
        // affects unitsList
        //$this->newUnitsList = RlstUnit::whereIn('unit_ty', $this->unitTypesList)->pluck('id')->toArray();
        $this->newUnitsList = DB::table('rlst_units')->whereIn('unit_ty', $unitTypesList)->pluck('id')->toArray();

        //Log::channel('custom')->info('unitTypesList = '. json_encode($this->unitTypesList));
        //Log::channel('custom')->info('newUnitsList = '. json_encode($this->newUnitsList));

        if($this->checkDiff($this->unitsList, $this->newUnitsList))
            $this->updateUnitsList();

    }

    public function updateUnitsList()
    {   
            //Log::channel('custom')->info('entered updateUnitsList');

            if($this->customEmpty($this->newUnitsList) || $this->nullUnitsList){
                $this->nullUnitsList = true;
                $this->unitsList = [];
                return;
            }
                
        
            $this->unitsList = $this->getArraysOverlap($this->newUnitsList,$this->unitsList);
            
            if(!$this->request->unit_ids)
                $unitsList = $this->unitsList;
            else
                $unitsList = $this->request->unit_ids;

            //$this->newData = RlstUnit::whereIn('id', $this->unitsList)
            $this->newData = DB::table('rlst_units')->whereIn('id', $unitsList)
            ->select('building_id','view','finishing','unit_ty','unit_status_id','unit_area','rooms','path')
            ->get();

            // affects buildingsList, viewsList, finishingsList, unitTypesList, unitStatusesList, unitAreaFrom, unitAreaTo, unitRoomsFrom, unitRoomsTo, unitBathroomsFrom, unitBathroomsTo
            $this->newBuildingsList = $this->newData->pluck('building_id')->unique()->values()->toArray();
            $this->newViewsList = $this->newData->pluck('view')->unique()->values()->toArray();
            $this->newFinishingsList = $this->newData->pluck('finishing')->unique()->values()->toArray();
            $this->newUnitTypesList = $this->newData->pluck('unit_ty')->unique()->values()->toArray();
            $this->newUnitStatusesList = $this->newData->pluck('unit_status_id')->unique()->values()->toArray();

            $this->newUnitAreaFrom = $this->newData->min('unit_area');
            $this->newUnitAreaTo = $this->newData->max('unit_area');
            $this->newUnitRoomsFrom = $this->newData->min('rooms');
            $this->newUnitRoomsTo = $this->newData->max('rooms');
            $this->newUnitBathroomsFrom = $this->newData->min('path');
            $this->newUnitBathroomsTo = $this->newData->max('path');

            

            //Log::channel('custom')->info('unitsList = '. json_encode($this->unitsList));

            //Log::channel('custom')->info('newBuildingsList = '. json_encode($this->newBuildingsList));
            //Log::channel('custom')->info('newViewsList = '. json_encode($this->newViewsList));
            //Log::channel('custom')->info('newFinishingsList = '. json_encode($this->newFinishingsList));
            //Log::channel('custom')->info('newUnitTypesList = '. json_encode($this->newUnitTypesList));
            //Log::channel('custom')->info('newUnitStatusesList = '. json_encode($this->newUnitStatusesList));
            //Log::channel('custom')->info('newUnitAreaFrom = '. $this->newUnitAreaFrom);
            //Log::channel('custom')->info('newUnitAreaTo = '. $this->newUnitAreaTo);
            //Log::channel('custom')->info('newUnitRoomsFrom = '. $this->newUnitRoomsFrom);
            //Log::channel('custom')->info('newUnitRoomsTo = '. $this->newUnitRoomsTo);
            //Log::channel('custom')->info('newUnitBathroomsFrom = '. $this->newUnitBathroomsFrom);
            //Log::channel('custom')->info('newUnitBathroomsTo = '. $this->newUnitBathroomsTo);


            if($this->checkDiff($this->buildingsList, $this->newBuildingsList))
                $this->updateBuildingsList();

            if($this->checkDiff($this->viewsList, $this->newViewsList))
                $this->updateViewsList();

            if($this->checkDiff($this->finishingsList, $this->newFinishingsList))
                $this->updateFinishingsList();

            if($this->checkDiff($this->unitTypesList, $this->newUnitTypesList))
                $this->updateUnitTypesList();

            if($this->checkDiff($this->unitStatusesList, $this->newUnitStatusesList))
                $this->updateUnitStatuses();


            if($this->newUnitAreaFrom < $this->unitAreaFrom || $this->newUnitAreaTo > $this->unitAreaTo)
                $this->updateUnitArea();

            if($this->newUnitRoomsFrom < $this->unitRoomsFrom || $this->newUnitRoomsTo > $this->unitRoomsTo)
                $this->updateUnitRooms();

            if($this->newUnitBathroomsFrom < $this->unitBathroomsFrom || $this->newUnitBathroomsTo > $this->unitBathroomsTo)
                $this->updateUnitBathrooms();

        
    }

    public function updateBuildingsList()
    {   
            //Log::channel('custom')->info('entered updateBuildingsList');

            if($this->customEmpty($this->newBuildingsList) || $this->nullBuildingsList){
                $this->nullBuildingsList = true;
                $this->buildingsList = [];
                return;
            }

            $this->tableBuildingWallet_Building = true;
            $this->tableBuildingPolicy_Building = true;

            
            $this->buildingsList = $this->getArraysOverlap($this->newBuildingsList,$this->buildingsList);

            if(!$this->request->building_ids)
                $buildingsList = $this->buildingsList;
            else
                $buildingsList = $this->request->building_ids;
            
            // affects walletsList, policiesList, streetsList, buildingTypesList, unitsList
            //$this->newWalletsList = RlstBuildingWallet::whereIn('building_id', $buildingIds)->pluck('wallet_id')->toArray();  
            $this->newWalletsList = DB::table('rlst_building_wallet')->whereIn('building_id', $buildingsList)->pluck('wallet_id')->toArray();
            
            //$this->newPoliciesList = RlstBuildingPolicy::whereIn('building_id', $buildingIds)->pluck('policy_id')->toArray();
            $this->newPoliciesList = DB::table('rlst_building_policies')->whereIn('building_id', $buildingsList)->pluck('policy_id')->toArray();

            //$this->newData = RlstBuilding::whereIn('id', $buildingIds)
            $this->newData = DB::table('rlst_buildings')->whereIn('id', $buildingsList)
            ->select('street_id','building_type_id')->get();

            $this->newStreetsList = $this->newData->pluck('street_id')->unique()->values()->toArray();
            $this->newBuildingTypesList = $this->newData->pluck('building_type_id')->unique()->values()->toArray();

            //$this->newUnitsList = RlstUnit::whereIn('building_id', $buildingIds)->pluck('id')->toArray();
            $this->newUnitsList = DB::table('rlst_units')->whereIn('building_id', $buildingsList)->pluck('id')->toArray();    

            //Log::channel('custom')->info('buildingsList = '. json_encode($buildingsList));
            //Log::channel('custom')->info('newWalletsList = '. json_encode($this->newWalletsList));
            //Log::channel('custom')->info('newPoliciesList = '. json_encode($this->newPoliciesList));        

            //Log::channel('custom')->info('newStreetsList = '. json_encode($this->newStreetsList));
            //Log::channel('custom')->info('newBuildingTypesList = '. json_encode($this->newBuildingTypesList));
            //Log::channel('custom')->info('newUnitsList = '. json_encode($this->newUnitsList));

            
            
            if($this->checkDiff($this->policiesList, $this->newPoliciesList) 
                && !($this->tableBuildingPolicy_Building && $this->tableBuildingPolicy_Policy))
                $this->updatePoliciesList();
            

            $this->tableBuildingPolicy_Building = false;
            
            
            if($this->checkDiff($this->walletsList, $this->newWalletsList) &&
             !($this->tableBuildingWallet_Building && $this->tableBuildingWallet_Wallet))
                $this->updateWalletsList();

            $this->tableBuildingWallet_Building = false;
            

            if($this->checkDiff($this->streetsList, $this->newStreetsList))
                $this->updateStreetsList();

            if($this->checkDiff($this->buildingTypesList, $this->newBuildingTypesList))
                $this->updateBuildingTypesList();  

            if($this->checkDiff($this->unitsList, $this->newUnitsList))
                $this->updateUnitsList();

    

    }

    
    public function updateWalletsList()
    {   
            //Log::channel('custom')->info('entered updateWalletsList');

            if($this->customEmpty($this->newWalletsList) || $this->nullWalletsList){
                $this->nullWalletsList = true;
                $this->walletsList = [];
                return;
            }


            $this->tableBuildingWallet_Wallet = true;
            $this->tableWalletOwner_Wallet = true;

            $this->walletsList = $this->getArraysOverlap($this->newWalletsList,$this->walletsList);

            if(!$this->request->wallet_ids)
                $walletsList = $this->walletsList;
            else
                $walletsList = $this->request->wallet_ids;
            
            // affects ownersList, buildingsList

            //$this->newOwnersList = RlstWalletOwner::whereIn('wallet_id', $this->walletsList)->pluck('owner_id')->toArray();
            $this->newOwnersList = DB::table('rlst_wallet_owners')->whereIn('wallet_id', $walletsList)->pluck('owner_id')->toArray();

            //$this->newBuildingsList = RlstBuildingWallet::whereIn('wallet_id', $this->walletsList)->pluck('building_id')->toArray();
            $this->newBuildingsList = DB::table('rlst_building_wallet')->whereIn('wallet_id', $walletsList)->pluck('building_id')->toArray();

            //Log::channel('custom')->info('walletsList = '. json_encode($walletsList));
            //Log::channel('custom')->info('newOwnersList = '. json_encode($this->newOwnersList));
            //Log::channel('custom')->info('newBuildingsList = '. json_encode($this->newBuildingsList));

            
            if($this->checkDiff($this->ownersList, $this->newOwnersList) &&
                !($this->tableWalletOwner_Wallet && $this->tableWalletOwner_Owner))
            
                 $this->updateOwnersList();

            $this->tableWalletOwner_Wallet = false;

            if($this->checkDiff($this->buildingsList, $this->newBuildingsList) &&
                !($this->tableBuildingWallet_Building && $this->tableBuildingWallet_Wallet))

                $this->updateBuildingsList();

            $this->tableBuildingWallet_Wallet = false;
            
    }


    public function updatePoliciesList()
    {   
        //Log::channel('custom')->info('entered updatePoliciesList');

        if($this->customEmpty($this->newPoliciesList) || $this->nullPoliciesList){
            $this->nullPoliciesList = true;
            $this->policiesList = [];
            return;
        }
        
        $this->tableBuildingPolicy_Policy = true;

        $this->policiesList = $this->getArraysOverlap($this->newPoliciesList,$this->policiesList);

        if(!$this->request->policy_ids)
            $policiesList = $this->policiesList;
        else
            $policiesList = $this->request->policy_ids;
         
        // affects buildingsList
        //$this->newBuildingsList = RlstBuildingPolicy::whereIn('policy_id', $this->policiesList)->pluck('building_id')->toArray();
        $this->newBuildingsList = DB::table('rlst_building_policies')->whereIn('policy_id', $policiesList)->pluck('building_id')->toArray();

        //Log::channel('custom')->info('policiesList = '. json_encode($policiesList));
        //Log::channel('custom')->info('newBuildingsList = '. json_encode($this->newBuildingsList));

        if($this->checkDiff($this->buildingsList, $this->newBuildingsList) && 
            !($this->tableBuildingPolicy_Building && $this->tableBuildingPolicy_Policy))
                $this->updateBuildingsList();
        
        $this->tableBuildingPolicy_Policy = false;


        
    }

    public function updateCountriesList()
    {
            //Log::channel('custom')->info('entered updateCountriesList');

            if($this->customEmpty($this->newCountriesList) || $this->nullCountriesList){
                $this->nullCountriesList = true;
                $this->countriesList = [];
                return;
            }

            $this->countriesList = $this->getArraysOverlap($this->newCountriesList,$this->countriesList);

            if(!$this->request->country_ids)
                $countriesList = $this->countriesList;
            else
                $countriesList = $this->request->country_ids;
            
            //$this->newGovernoratesList = Governorate::whereIn('country_id', $this->countriesList)->pluck('id')->toArray();
            $this->newGovernoratesList = DB::table('general_governorates')->whereIn('country_id', $countriesList)->pluck('id')->toArray();

            //Log::channel('custom')->info('countriesList = '. json_encode($countriesList));
            //Log::channel('custom')->info('newGovernoratesList = '. json_encode($this->newGovernoratesList));
            
            if($this->checkDiff($this->governoratesList, $this->newGovernoratesList))
                $this->updateGovernoratesList();

    }

    public function updateGovernoratesList()
    {   
        
            //Log::channel('custom')->info('entered updateGovernoratesList');

            if($this->customEmpty($this->newGovernoratesList) || $this->nullGovernoratesList){
                $this->nullGovernoratesList = true;
                $this->governoratesList = [];
                return;
            }

            $this->governoratesList = $this->getArraysOverlap($this->newGovernoratesList,$this->governoratesList);

            if(!$this->request->governorate_ids)
                $governoratesList =$this->governoratesList;
            else
                $governoratesList = $this->request->governorate_ids;
            
            //$this->newCitiesList = City::whereIn('governorate_id', $this->governoratesList)->pluck('id')->toArray();
            $this->newCitiesList = DB::table('general_cities')->whereIn('governorate_id', $governoratesList)->pluck('id')->toArray();

            //$this->newCountriesList = Governorate::whereIn('id', $this->governoratesList)->pluck('country_id')->toArray();
            $this->newCountriesList = DB::table('general_governorates')->whereIn('id', $governoratesList)->pluck('country_id')->toArray();
                        
            //Log::channel('custom')->info('governoratesList = '. json_encode($governoratesList));
            //Log::channel('custom')->info('newCitiesList = '. json_encode($this->newCitiesList));
            //Log::channel('custom')->info('newCountriesList = '. json_encode($this->newCountriesList));

            if($this->checkDiff($this->citiesList, $this->newCitiesList))
                $this->updateCitiesList();

            if($this->checkDiff($this->countriesList, $this->newCountriesList))
                $this->updateCountriesList();

    }

    public function updateCitiesList()
    {   
            //Log::channel('custom')->info('entered updateCitiesList');

            if($this->customEmpty($this->newCitiesList) || $this->nullCitiesList){
                $this->nullCitiesList = true;
                $this->citiesList = [];
                return;
            }
        
            $this->citiesList = $this->getArraysOverlap($this->newCitiesList,$this->citiesList);

            $citiesList = $this->request->city_ids ? $this->request->city_ids : $this->citiesList;
            Log::channel('custom')->info('citiesList = '. json_encode($citiesList));

            
            
            
            

            //$this->newAvenuesList = Avenue::whereIn('city_id', $this->citiesList)->pluck('id')->toArray();
            $this->newAvenuesList = DB::table('general_avenues')->whereIn('city_id', $citiesList)->pluck('id')->toArray();

            //$this->newGovernoratesList = City::whereIn('id', $this->citiesList)->pluck('governorate_id')->toArray();
            $this->newGovernoratesList = DB::table('general_cities')->whereIn('id', $citiesList)->pluck('governorate_id')->toArray();

            //Log::channel('custom')->info('citiesList = '. json_encode($citiesList));
            //Log::channel('custom')->info('newAvenuesList = '. json_encode($this->newAvenuesList));
            //Log::channel('custom')->info('newGovernoratesList = '. json_encode($this->newGovernoratesList));


            if($this->checkDiff($this->avenuesList, $this->newAvenuesList))
                 $this->updateAvenuesList(); 

            if($this->checkDiff($this->governoratesList, $this->newGovernoratesList))
                $this->updateGovernoratesList();
        
    }

    public function updateAvenuesList()
    {   
            //Log::channel('custom')->info('entered updateAvenuesList');
        
            if($this->customEmpty($this->newAvenuesList) || $this->nullAvenuesList){
                $this->nullAvenuesList = true;
                $this->avenuesList = [];
                return;
            }

            $this->avenuesList = $this->getArraysOverlap($this->newAvenuesList,$this->avenuesList);

            $avenuesList = $this->request->avenue_ids ? $this->request->avenue_ids : $this->avenuesList;
                
            
            //$this->newStreetsList = Street::whereIn('avenue_id', $this->avenuesList)->pluck('id')->toArray();
            $this->newStreetsList = DB::table('general_streets')->whereIn('avenue_id', $avenuesList)->pluck('id')->toArray();

            //$this->newCitiesList = Avenue::whereIn('id', $this->avenuesList)->pluck('city_id')->toArray();
            $this->newCitiesList = DB::table('general_avenues')->whereIn('id', $avenuesList)->pluck('city_id')->toArray();
            
            //Log::channel('custom')->info('avenuesList = '. json_encode($avenuesList));
            //Log::channel('custom')->info('newStreetsList = '. json_encode($this->newStreetsList));
            //Log::channel('custom')->info('newCitiesList = '. json_encode($this->newCitiesList));

            if($this->checkDiff($this->streetsList, $this->newStreetsList))
                $this->updateStreetsList();

            if($this->checkDiff($this->citiesList, $this->newCitiesList))
                $this->updateCitiesList();

    }

    public function updateStreetsList()
    {   
        
            //Log::channel('custom')->info('entered updateStreetsList');

            if($this->customEmpty($this->newStreetsList) || $this->nullStreetsList){
                $this->nullStreetsList = true;
                $this->streetsList = [];
                return;
            }

            $this->streetsList = $this->getArraysOverlap($this->newStreetsList,$this->streetsList);

            $streetsList = $this->request->street_ids ? $this->request->street_ids : $this->streetsList;
            
            //$this->newBuildingsList = RlstBuilding::whereIn('street_id', $this->streetsList)->pluck('id')->toArray();
            $this->newBuildingsList = DB::table('rlst_buildings')->whereIn('street_id', $streetsList)->pluck('id')->toArray();

            //$this->newAvenuesList = Street::whereIn('id', $this->streetsList)->pluck('avenue_id')->toArray();
            $this->newAvenuesList = DB::table('general_streets')->whereIn('id', $streetsList)->pluck('avenue_id')->toArray();
            
            //Log::channel('custom')->info('streetsList = '. json_encode($streetsList));
            //Log::channel('custom')->info('newBuildingsList = '. json_encode($this->newBuildingsList));
            //Log::channel('custom')->info('newAvenuesList = '. json_encode($this->newAvenuesList));

            if($this->checkDiff($this->buildingsList, $this->newBuildingsList))
                $this->updateBuildingsList();

            if($this->checkDiff($this->avenuesList, $this->newAvenuesList))
                $this->updateAvenuesList();

    }

    public function updateBuildingTypesList()
    {   
            //Log::channel('custom')->info('entered updateBuildingTypesList');

            if($this->customEmpty($this->newBuildingTypesList) || $this->nullBuildingTypesList){
                $this->nullBuildingTypesList = true;
                $this->buildingTypesList = [];
                return;
            }
        
            $this->buildingTypesList = $this->getArraysOverlap($this->newBuildingTypesList,$this->buildingTypesList);

           $buildingTypesList = $this->request->building_type_ids ? $this->request->building_type_ids : $this->buildingTypesList;

            
            //$this->newBuildingsList = RlstBuilding::whereIn('building_type_id', $this->buildingTypesList)->pluck('id')->toArray();
            $this->newBuildingsList = DB::table('rlst_buildings')->whereIn('building_type_id', $buildingTypesList)->pluck('id')->toArray();
            
            //Log::channel('custom')->info('buildingTypesList = '. json_encode($buildingTypesList));
            //Log::channel('custom')->info('newBuildingsList = '. json_encode($this->newBuildingsList));

            if($this->checkDiff($this->buildingsList, $this->newBuildingsList))
                $this->updateBuildingsList();

    }

    public function updateOwnersList()
    {   
            //Log::channel('custom')->info('entered updateOwnersList');

            if($this->customEmpty($this->newOwnersList) || $this->nullOwnersList){
                $this->nullOwnersList = true;
                $this->ownersList = [];
                return;
            }

            $this->tableWalletOwner_Owner = true;

            $this->ownersList = $this->getArraysOverlap($this->newOwnersList,$this->ownersList); 

            $ownersList = $this->request->owner_ids ? $this->request->owner_ids : $this->ownersList;
            
            //$this->newWalletsList = RlstWalletOwner::whereIn('owner_id', $this->ownersList)->pluck('wallet_id')->toArray();
            $this->newWalletsList = DB::table('rlst_wallet_owners')->whereIn('owner_id', $ownersList)->pluck('wallet_id')->toArray();

            //Log::channel('custom')->info('ownersList = '. json_encode($ownersList));
            //Log::channel('custom')->info('newWalletsList = '. json_encode($this->newWalletsList));

            if($this->checkDiff($this->walletsList, $this->newWalletsList) && 
                !($this->tableWalletOwner_Wallet && $this->tableWalletOwner_Owner))
                $this->updateWalletsList();

            $this->tableWalletOwner_Owner = false;
        
    }



    
    public function updateUnitStatuses()
    {   
            //Log::channel('custom')->info('entered updateUnitStatuses');

            if($this->customEmpty($this->newUnitStatusesList) || $this->nullUnitStatusesList){
                $this->nullUnitStatusesList = true;
                $this->unitStatusesList = [];
                return;
            }

            $this->unitStatusesList = $this->getArraysOverlap($this->newUnitStatusesList,$this->unitStatusesList);

            $unitStatusesList = $this->request->unit_status_ids ? $this->request->unit_status_ids : $this->unitStatusesList;      
            
            //$this->newUnitsList = RlstUnit::whereIn('unit_status_id', $unitStatusesList)->pluck('id')->toArray();
            $this->newUnitsList = DB::table('rlst_units')->whereIn('unit_status_id', $unitStatusesList)->pluck('id')->toArray();

            //Log::channel('custom')->info('unitStatusesList = '. json_encode($unitStatusesList));
            //Log::channel('custom')->info('newUnitsList = '. json_encode($this->newUnitsList));

            if($this->checkDiff($this->unitsList, $this->newUnitsList))
                $this->updateUnitsList();
        
    }


    public function updateUnitArea()
    {   
        //Log::channel('custom')->info('entered updateUnitArea');

        if($this->newUnitAreaFrom < $this->unitAreaFrom) // if the new value is less than the old one (min)
            $this->unitAreaFrom = $this->newUnitAreaFrom;
        
        if($this->newUnitAreaTo > $this->unitAreaTo) // if the new value is greater than the old one (max)
            $this->unitAreaTo = $this->newUnitAreaTo;

        //Log::channel('custom')->info('unitAreaFrom = '. $this->unitAreaFrom);
        //Log::channel('custom')->info('unitAreaTo = '. $this->unitAreaTo);
    }

    public function updateUnitRooms()
    {   
        //Log::channel('custom')->info('entered updateUnitRooms');
        if($this->newUnitRoomsFrom < $this->unitRoomsFrom) // if the new value is less than the old one (min)
            $this->unitRoomsFrom = $this->newUnitRoomsFrom;

        if($this->newUnitRoomsTo > $this->unitRoomsTo) // if the new value is greater than the old one (max)
            $this->unitRoomsTo = $this->newUnitRoomsTo;

        //Log::channel('custom')->info('unitRoomsFrom = '. $this->unitRoomsFrom);
        //Log::channel('custom')->info('unitRoomsTo = '. $this->unitRoomsTo);
                  
    }

    public function updateUnitBathrooms()
    {   
        //Log::channel('custom')->info('entered updateUnitBathrooms');
        
        if($this->newUnitBathroomsFrom < $this->unitBathroomsFrom) // if the new value is less than the old one (min)
            $this->unitBathroomsFrom = $this->newUnitBathroomsFrom;

        if($this->newUnitBathroomsTo > $this->unitBathroomsTo) // if the new value is greater than the old one (max)
            $this->unitBathroomsTo = $this->newUnitBathroomsTo;

        //Log::channel('custom')->info('unitBathroomsFrom = '. $this->unitBathroomsFrom);
        //Log::channel('custom')->info('unitBathroomsTo = '. $this->unitBathroomsTo);
    }

    public function checkDiff($array1, $array2)
    {
        
        $diff1 = array_values(array_diff($array1, $array2)); // asking if the diff array is empty or not
        $diff2 = array_values(array_diff($array2, $array1)); // asking if the diff array is empty or not
        $diff = array_merge($diff1,$diff2);

        return sizeOf($diff) > 0;

        /*
        $a = sort($array1);
        $b = sort($array2);

        return !($a==$b);
        */        
    }

    public function getArraysOverlap($array1, $array2)
    {
        $a = array_values(array_intersect($array1, $array2));
        $b = array_values(array_intersect($array2, $array1));
        
        $c = array_values(array_unique(array_merge($a, $b)));
        $d = array_values(array_unique(array_merge($array1,$array2)));

        if($this->customEmpty($c))
            return $d;
        
        return $c;
    }



    public function customArrayDiff($arr1, $arr2) {
        $count1 = array_count_values($arr1);
        $count2 = array_count_values($arr2);
    
        foreach ($count1 as $key => $value) {
            if (!isset($count2[$key]) || $count2[$key] !== $value) {
                return true;
            }
        }
    
        foreach ($count2 as $key => $value) {
            if (!isset($count1[$key]) || $count1[$key] !== $value) {
                return true;
            }
        }
    
        return false;
    }

    public function customEmpty($array)
    {
        $arrayNoNullOrZero = array_filter($array, function($value) {
             return $value !== null && $value !== 0; 
            });
        return empty($arrayNoNullOrZero);
    }

    public function getColumnFKName($tableName, $columnName)
    {
        $foreignKeyName = DB::selectOne("
        SELECT 
            CONSTRAINT_NAME 
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            TABLE_SCHEMA = ? AND 
            TABLE_NAME = ? AND 
            COLUMN_NAME = ? AND 
            REFERENCED_TABLE_NAME IS NOT NULL
        ", [env('DB_DATABASE'), $tableName, $columnName])->CONSTRAINT_NAME; 
    
        return $foreignKeyName;
    }

    public function getDirectlyDependentTablesWithFKs()
    {
        $tables = DB::select("
        SELECT 
            TABLE_NAME, 
            COLUMN_NAME, 
            REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME 
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            TABLE_SCHEMA = ? AND 
            REFERENCED_TABLE_NAME IS NOT NULL
        ", [env('DB_DATABASE')]); 
    
        $tables = collect($tables)->groupBy('TABLE_NAME')->toArray();

        return $tables;
    }

}
