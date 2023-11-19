<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstTenant;
use Modules\RealEstate\Http\Requests\RlstTenantRequest;
use Modules\RealEstate\Transformers\RlstTenantResource;

class RlstTenantController extends Controller
{

    public function __construct(private RlstTenant $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {

        $model = $this->model->data()->find($id);

        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstTenantResource($model));
    }

    public function all(Request $request)
    {
        
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RlstTenantResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstTenantRequest $request)
    {

        $model = $this->model->create($request->validated());

        return responseJson(200, 'created', new RlstTenantResource($model));

    }

    public function update($id, RlstTenantRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new RlstTenantResource($model));

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
            $model = $this->model->find($id);
            $model->delete();
            $model->refresh();
            
        }

        return responseJson(200, 'deleted');
    }

    public function getDropDown()
    {
        $models = $this->model->select('id', 'name','name_e')->get();


        return responseJson(200, 'success', $models);
    }
}
