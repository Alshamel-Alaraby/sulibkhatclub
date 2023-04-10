<?php

namespace Modules\BoardsRent\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\BoardsRent\Entities\Order;
use Modules\BoardsRent\Http\Requests\OrderRequest;
use Modules\BoardsRent\Transformers\OrderResource;

class OrderController extends Controller
{
    public function __construct(private Order $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new OrderResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', OrderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(OrderRequest $request)
    {
        // return $this->model;
        $model =  DB::transaction(function () use ($request) {
            $model = $this->model->create($request->validated());
            $model->refresh();
            foreach ($request->details as $detail) {
                $model->details()->create($detail);
            }
            return $model;
        });

        $model->update([
            "serial_number" => generalSerial($model, "Order"),
        ]);

        return responseJson(200, 'created', new OrderResource($model));
    }

    public function update($id, OrderRequest $request)
    {
        DB::transaction(function () use ($request, $id) {
            $model = $this->model->find($id);
            if (!$model) {
                return responseJson(404, 'not found');
            }

            $model->update($request->validated());
            $model->refresh();
            foreach ($request->details as $detail) {
                $model->details()->create($detail);
            }
            if ($request->deleted_details) {
                $model->details()->whereIn('id', $request->deleted_details)->delete();
            }

            return responseJson(200, 'updated', new OrderResource($model));
        });
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
        $model->details()->delete();

        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->details()->delete();
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }
}