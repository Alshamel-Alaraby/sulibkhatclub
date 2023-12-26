<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneralInsuranceSettingRequest;
use App\Http\Resources\GeneralInsuranceSettingResource;
use App\Models\GeneralInsuranceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralInsuranceSettingController extends Controller
{
    public function __construct(private GeneralInsuranceSetting $model)
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

        return responseJson(200, 'success', GeneralInsuranceSettingResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new GeneralInsuranceSettingResource($model));
    }

    public function create(GeneralInsuranceSettingRequest $request)
    {
        $model = $this->model->create($request->validated());
        return responseJson(200, 'success');

    }

    public function update($id, GeneralInsuranceSettingRequest $request)
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
        $documentsCount = DB::table('general_insurance_documents')->count();

        if ($documentsCount > 0) {
            return responseJson(400, 'Cannot delete.  insurance documents exist.');
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

    public function getTables()
    {
        $tables = DB::select('SHOW TABLES');
        $data = [];
        foreach ($tables as $table) {
            foreach ($table as $key => $value) {
                if (str_contains($value, 'general') || str_contains($value, 'car') ) {
                    array_push($data, $value);
                }
            }
        }
        return responseJson(200, 'success', $data);
    }

    public function getColumns($table)
    {
        $columns = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM ' . $table);
        $data = [];
        foreach ($columns as $column) {
            array_push($data, $column->Field);
        }
        return responseJson(200, 'success', $data);
    }
}
