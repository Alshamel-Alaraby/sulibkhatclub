<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\RealEstate\Entities\RlstUnit;
use Modules\RealEstate\Entities\RlstUnitService;
use Modules\RealEstate\Http\Requests\RlstUnitServiceRequest;
use Modules\RealEstate\Transformers\RlstUnitServiceResource;
use Modules\RealEstate\Transformers\RlstUnitServicesCollectionResource;

class RlstUnitServiceController extends Controller
{

    public function __construct(private RlstUnitService $model,private RlstUnit $unitModel)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstUnitServiceResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->unitModel->with('services')->has('services')->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request['unit_id']){
            $models->whereIn('id',$request['unit_id']);
        }
//        if ($request->last_date){
//           return $last =  $this->model->selectRaw("unit_id,service_id,MAX(from_date),default_price")->groupBy(['unit_id','service_id'])->get();
//        }

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstUnitServicesCollectionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(RlstUnitServiceRequest $request)
    {
        foreach ($request->details as $item) {
            $unitService = [
                'unit_id' => $request->unit_id,
                'service_id' => $item['service_id'],
                'default_price' => $item['default_price'],
                'from_date' => $item['from_date'],
            ];
            $this->model->create($unitService);
        }
        return responseJson(200, 'success');
    }

    public function update($id, RlstUnitServiceRequest $request)
    {
        $model = $this->unitModel->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->services->unique('id')->map(function($item){
            $pivot = $item->pivot;
            RlstUnitService::where('unit_id',$pivot->unit_id)->where('service_id',$pivot->service_id)->where('from_date',$pivot->from_date)->where('default_price',$pivot->default_price)->delete();
        });

        foreach ($request->details as $item) {
            $unitService = [
                'unit_id' => $request->unit_id,
                'service_id' => $item['service_id'],
                'default_price' => $item['default_price'],
                'from_date' => $item['from_date'],
            ];
            $this->model->create($unitService);
        }
        return responseJson(200, 'updated',[]);
    }


    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->unitModel->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        RlstUnitService::where('unit_id',$id)->delete();

        return responseJson(200, 'deleted');
    }

    public function bulkDelete(Request $request)
    {

        if (!$request->ids) {
            return responseJson(400, 'ids is required');
        }
        $models = RlstUnitService::whereIn('unit_id',$request->ids)->delete();

        return responseJson(200, 'deleted');
    }


}
