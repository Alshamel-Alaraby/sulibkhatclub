<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstRevenueItem;
use Modules\RealEstate\Http\Requests\RlstRevenueItemRequest;
use Modules\RealEstate\Transformers\RlstRevenueItemResource;

class RlstRevenueItemController extends Controller
{

    public function __construct(private RlstRevenueItem $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {

        $model = $this->model->data()->find($id);

        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstRevenueItemResource($model));
    }

    public function all(Request $request)
    {
        
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstRevenueItemResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstRevenueItemRequest $request)
    {

        $model = $this->model->create($request->validated());
        return $model;


        return responseJson(200, 'created', new RlstRevenueItemResource($model));

    }

    public function update($id, RlstRevenueItemRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new RlstRevenueItemResource($model));

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

        return responseJson(200, 'success');
    }



    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
        
            $model->delete();
            $model->refresh();
            
        }

        return responseJson(200, 'success');
    }


    public function getDropDown()
    {
        $models = $this->model->data()->get();

        return responseJson(200, 'success', $models);
    }
}
