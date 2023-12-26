<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralInsuranceDocumentRequest;
use App\Http\Resources\GeneralInsuranceDocumentResource;
use App\Models\GeneralInsuranceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralInsuranceDocumentController extends Controller
{
    public function __construct(private GeneralInsuranceDocument $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->data()->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', GeneralInsuranceDocumentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new GeneralInsuranceDocumentResource($model));
    }

    public function create(GeneralInsuranceDocumentRequest $request)
    {
        $model = $this->model->create($request->validated());
        return responseJson(200, 'success');

    }

    public function update($id, GeneralInsuranceDocumentRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated');
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));

        }

        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->model->find($id);
            $arr = [];
            if ($model->hasChildren()) {
                $arr[] = $id;
                continue;
            }
            $this->model->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation can\'t delete'));
        }
        return responseJson(200, __('Done'));
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

    public function getTableData(Request $request)
    {
        $models = DB::table($request->db_table)->get();

        return responseJson(200, 'success', $models);
    }

}
