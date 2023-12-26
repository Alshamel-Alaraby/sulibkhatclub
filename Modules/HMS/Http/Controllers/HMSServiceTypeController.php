<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSServiceType;
use Modules\HMS\Http\Requests\HMSServiceTypeRequest;
use Modules\HMS\Transformers\HMSServiceTypeResource;

class HMSServiceTypeController extends Controller
{
    public function __construct(private HMSServiceType $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSServiceTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSServiceTypeRequest $request)
    {
        $this->model->create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSServiceTypeRequest $request, HMSServiceType $service_type)
    {
        $service_type->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSServiceType $service_type)
    {
        $service_type->delete();
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();

        $models->each(function ($model) {
//            if (!$model->hasChildren()) {
            $model->delete();
//            }

        });
        return responseJson(200, 'deleted');
    }

}
