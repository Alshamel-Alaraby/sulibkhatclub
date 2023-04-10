<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Http\Requests\PaymentMethod\PaymentMethodRequest;
use App\Http\Resources\PaymentMethod\PaymentMethodResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentMethodController extends Controller
{
    public function __construct(private \App\Repositories\PaymentMethod\PaymentMethodInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if ($model) {
            return responseJson(200, 'success', new PaymentMethodResource($model));
        } else {
            return responseJson(404, __('message.data not found'));
        }

    }

    public function all(Request $request)
    {


        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', PaymentMethodResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(PaymentMethodRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new PaymentMethodResource($model));
    }

    public function update(PaymentMethodRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new PaymentMethodResource($model));
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

}
