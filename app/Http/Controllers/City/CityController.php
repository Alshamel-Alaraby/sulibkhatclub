<?php

namespace App\Http\Controllers\City;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Http\Resources\City\CityResource;
use App\Repositories\City\CityRepositoryInterface;
use App\Traits\CanDeleteTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    use CanDeleteTrait;

    public $repository;
    public $resource = CityResource::class;
    public function __construct(CityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function index(Request $request)
    {

        // if (count($_GET) > 0) {
        //     cacheForget('cities');
        // }
        // $data = cacheGet('cities');
        // if ($request->search || $request->is_active) {
        //     cacheForget('cities');
        //     $data = $this->repository->getAll($request);
        // }
        // if (!$data) {
        //     $data = $this->repository->getAll($request);
        //     cachePut('cities', $data);
        // }
        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', ($this->resource)::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \response
     */
    public function store(CityRequest $request)
    {

        if (!DB::table('general_countries')->find($request->country_id)) {
            return responseJson(404, __('country does\'t exist'));
        }
        if (!DB::table('general_governorates')->find($request->governorate_id)) {
            return responseJson(404, __('governorates does\'t exist'));
        }
        return $this->repository->create($request->validated());
        // return responseJson(200, __('done'));

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \response
     */
    public function show($id)
    {
        if ($city = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new $this->resource($city), 200);
        }
        return responseJson(404, __('not found'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \response
     */
    public function update(CityRequest $request, $id)
    {

        $data = [];
        if ($request->country_id) {
            if (!DB::table('general_countries')->find($request->country_id)) {
                return responseJson(422, __('countries does\'t exist'));
            }
            $data['country_id'] = $request->country_id;
        }

        if ($request->governorate_id) {
            if (!DB::table('general_governorates')->find($request->governorate_id)) {
                return responseJson(422, __('governorates does\'t exist'));
            }
            $data['governorate_id'] = $request->governorate_id;
        }
        $this->repository->update($request->validated(), $id);
        return responseJson(200, __('updated'));

    }

    public function destroy($id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('not found'));
        }
        if ($model->hasChildren()) {
            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
        }
        $this->repository->delete($id);
        return responseJson(200, __('deleted'));
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->repository->find($id);
            $arr = [];
            if ($model->hasChildren()) {
                $arr[] = $id;
                continue;
            }
            $this->repository->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
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
