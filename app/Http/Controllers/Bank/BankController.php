<?php

namespace App\Http\Controllers\Bank;

use App\Http\Requests\AllRequest;
use App\Http\Requests\Bank\StoreBankRequest;
use App\Http\Requests\BankRequest;
use App\Http\Resources\Bank\BankResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BankController extends Controller
{
    public function __construct(private \App\Repositories\Bank\BankInterface$modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        // $model = cacheGet('banks_' . $id);
        // if (!$model) {
        //     $model = $this->modelInterface->find($id);
        //     if (!$model) {
        //         return responseJson(404, __('message.data not found'));
        //     } else {
        //         cachePut('banks_' . $id, $model);
        //     }
        // }

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new BankResource($model));
    }

    public function all(AllRequest $request)
    {


        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', BankResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(StoreBankRequest $request)
    {
//        return $request;
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new BankResource($model));
    }

    public function update(BankRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new BankResource($model));
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

        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->hasChildren()) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }

}
