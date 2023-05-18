<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Http\Resources\Branch\BranchResource;
use App\Repositories\Branch\BranchRepositoryInterface;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public $repository;
    public $resource = BranchResource::class;
    public function __construct(BranchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {

        $branches = $this->repository->getAllBranches($request);

        return responseJson(200, 'success', ($this->resource)::collection($branches['data']), $branches['paginate'] ? getPaginates($branches['data']) : null);
    }

    public function store(BranchRequest $request)
    {
        cacheForget('branches');
        $this->repository->create($request->validated());
        return responseJson(200, __('done'));

    }

    public function show($id)
    {
        if ($branch = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new $this->resource($branch), 200);
        }
        return responseJson(404, __('not found'));
    }

    public function edit($id)
    {

    }

    public function update(BranchRequest $request, $id)
    {
        $data = [];
        $data['company_id'] = $request->company_id;
        if ($request->name) {
            $data['name'] = $request->name;
        }
        if ($request->name_e) {
            $data['name_e'] = $request->name_e;
        }
        if ($request->is_active) {
            $data['is_active'] = $request->is_active;
        }
        if ($request->parent_id) {
            $data['parent_id'] = $request->parent_id;
        }

        $this->repository->update($data, $id);
        return responseJson(200, __('updated'));

    }

    public function logs($id)
    {
        $model = $this->repository->find($id);

        if (!$model) {
            return responseJson(404, __('not found'));
        }
        $logs = $this->repository->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));

    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \response
     */
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

    public function processJsonData(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        $this->repository->processJsonData($data);

        return responseJson(200, __('Done'));
    }
}
