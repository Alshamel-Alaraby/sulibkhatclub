<?php

namespace App\Http\Controllers\GeneralCustomer;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralCustomer\CreateGeneralCustomerRequest;
use App\Http\Requests\GeneralCustomer\EditGeneralCustomerRequest;
use App\Http\Resources\GeneralCustomer\GeneralCustomerResource;
use App\Repositories\GeneralCustomer\GeneralCustomerRepositoryInterface;
use App\Traits\BulkDeleteTrait;
use App\Traits\CanDeleteTrait;
use Illuminate\Http\Request;

class GeneralCustomerController extends Controller
{
    use BulkDeleteTrait, CanDeleteTrait;

    protected $repository;
    protected $resource = GeneralCustomerResource::class;

    public function __construct(GeneralCustomerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        // if (count($_GET) == 0) {
        //     $models = cacheGet('GeneralCustomer');

        //     if (!$models) {
        //         $models = $this->repository->all($request);
        //         cachePut('GeneralCustomer', $models);
        //     }
        // } else {

        //     $models = $this->repository->all($request);
        // }
        $models = $this->repository->all($request);

        return responseJson(200, 'success', ($this->resource)::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function show($id)
    {

        // $model = cacheGet('GeneralCustomer_' . $id);

        // if (!$model) {
        //     $model = $this->repository->find($id);
        //     if (!$model) {
        //         return responseJson(404, __('message.data not found'));
        //     } else {
        //         cachePut('GeneralCustomer_' . $id, $model);
        //     }
        // }
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, __('Done'), new $this->resource($model));

    }

    public function store(CreateGeneralCustomerRequest $request)
    {
        $model = $this->repository->create($request->validated());
        return responseJson(200, 'success', new GeneralCustomerResource($model));
    }

    public function update(EditGeneralCustomerRequest $request, $id)
    {

        // $model = $this->repository->find($id);
        // if (!$model) {
        //     return responseJson(404, __('message.data not found'));
        // }
        // $model = $this->repository->update($request->validated(), $id);

        // return responseJson(200, __('Done'), new GeneralCustomerResource($model));

        $model = $this->repository->update($request, $id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        return responseJson(200, 'success', new GeneralCustomerResource($model));

    }

    public function logs($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }

}
