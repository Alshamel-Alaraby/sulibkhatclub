<?php

namespace Modules\ClubMembers\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Http\Requests\CmMemberTypeRequest;
use Modules\ClubMembers\Repositories\CmMemberStatus\CmMemberStatusInterface;
use Modules\ClubMembers\Transformers\CmMemberStatusResource;

class CmMemberStatusController extends Controller
{

    public function __construct(private CmMemberStatusInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }


    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmMemberStatusResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmMemberStatusResource($model));
    }


    public function create(CmMemberTypeRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new CmMemberStatusResource($model));
    }


    public function update(CmMemberTypeRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new CmMemberStatusResource($model));
    }


    public function delete($id)
    {
        $model = $this->modelInterface->find($id);

        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }


    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }


    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);

            $this->modelInterface->delete($id);
        }

        return responseJson(200, __('Done'));
    }

}
