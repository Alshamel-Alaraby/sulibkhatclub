<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Http\Requests\CmAcceptMembersRequest;
use Modules\ClubMembers\Http\Requests\CmMemberAcceptRequest;
use Modules\ClubMembers\Http\Requests\CmMemberDeclineRequest;
use Modules\ClubMembers\Http\Requests\CmMemberRequest;
use Modules\ClubMembers\Http\Requests\CmUpdateAcceptedMemberRequest;
use Modules\ClubMembers\Repositories\CmMember\CmMemberInterface;
use Modules\ClubMembers\Transformers\CmMemberResource;

class CmMemberController extends Controller
{

    public function __construct(private CmMemberInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;

    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function allAcceptancePending(Request $request)
    {
        $models = $this->modelInterface->allAcceptancePending($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(CmMemberRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function update(CmMemberRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function acceptMember(CmMemberAcceptRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->updateAcceptance($request, $id);

        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function declineMember(CmMemberDeclineRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->updateDecline($request, $id);

        return responseJson(200, 'success', new CmMemberResource($model));
    }

    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
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
            $arr = [];
            if ($model->have_children) {
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

    public function updateSponsor(Request $request, $sponsor_id)
    {

        $model = CmMember::where("sponsor_id", $sponsor_id)->get();

        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->updateSponsor($request, $sponsor_id);

        return responseJson(200, 'success', CmMemberResource::collection($model));

    }

    public function allAcceptance(Request $request)
    {
        $models = $this->modelInterface->allAcceptance($request);

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function acceptMembers(CmAcceptMembersRequest $request)
    {
        $this->modelInterface->acceptMembers($request->validated());

        return responseJson(200, 'updated successfully');

    }


    public function updateAcceptedMembers(CmUpdateAcceptedMemberRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);

        return responseJson(200, 'Updated Successfully', new CmMemberResource($model));
    }
}
