<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractTemplateRequest;
use App\Http\Resources\ContractTemplateResource;
use App\Models\ContractTemplate;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function __construct(private ContractTemplate $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new ContractTemplateResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', ContractTemplateResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(ContractTemplateRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created', new ContractTemplateResource($model));

    }

    public function update($id, ContractTemplateRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated', new ContractTemplateResource($model));
    }

    public function delete($id)
    {

        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $model->delete();
        $model->refresh();

        return responseJson(200, 'deleted');
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
        
}
