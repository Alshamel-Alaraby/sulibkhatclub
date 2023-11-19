<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstUnitService;
use Modules\RealEstate\Http\Requests\RlstUnitServiceRequest;
use Modules\RealEstate\Transformers\RlstUnitServiceResource;

class RlstUnitServiceController extends Controller
{

    public function __construct(private RlstUnitService $model)
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
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstUnitServiceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function create(RlstUnitServiceRequest $request)
    {
       //return $request['unit-service'][0];

        foreach ($request['unit-service'] as $unit_service) {
            $model = $this->model->create($unit_service);
        }
        $model->refresh();
        return responseJson(200, 'success', new RlstUnitServiceResource($model));
    }

    public function update($id, RlstUnitServiceRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        if ($request['unit-service']) {
            foreach ($request['unit-service'] as $unit_service) {
               $model->update($unit_service);
            }
        }
        return responseJson(200, 'updated', new RlstUnitServiceResource($model));
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
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->delete();
        $model->refresh();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete(Request $request)
    {

       
        if (!$request->ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
            $model->refresh();
        });
        return responseJson(200, 'deleted');
    }

    public function updateUnitArray($unitId, Request $request)
    {
        $unitServices = $this->model->where('unit_id',$unitId)->get();

        foreach ($unitServices as $unitService)
        {
            $unitService->delete();
            $unitService->refresh();
        }

        foreach ($request['unit-service'] as $data_create)
        {
            $this->model->create($data_create);
        }

    }
}
