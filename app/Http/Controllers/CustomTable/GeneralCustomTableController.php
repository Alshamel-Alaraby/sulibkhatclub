<?php

namespace App\Http\Controllers\CustomTable;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomTableRequest;
use App\Http\Resources\CustomTable\CustomTableResource;
use App\Models\GeneralCustomTable;
use Illuminate\Http\Request;

class GeneralCustomTableController extends Controller
{

    public function __construct(private \App\Repositories\CustomTable\CustomTableInterface$modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CustomTableResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', CustomTableResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(CustomTableRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        $model->refresh();
        return responseJson(200, 'success', new CustomTableResource($model));

    }

    public function update(CustomTableRequest $request)
    {
        $model = $this->modelInterface->update($request->validated());
        $model->refresh();
        return responseJson(200, 'success', new CustomTableResource($model));
    }
    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }

    public function getCustomTableFields($tableName)
    {
        $company_id = request()->header('company-id');
        $customTable = GeneralCustomTable::where([
            ["table_name", $tableName],
            ['company_id', $company_id],
        ])
            ->first();
        if (!$customTable) {
            $customTable = GeneralCustomTable::where([
                ["table_name", $tableName],
                ['company_id', 0],
            ])->first();
        }
        return $customTable ? $customTable->columns : [];
    }
}
