<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Http\Requests\CmMemberRequestRequest;
use Modules\ClubMembers\Repositories\CmMemberRequest\CmMemberRequestInterface;
use Modules\ClubMembers\Transformers\CmMemberRequestResource;
use Modules\ClubMembers\Entities\CmMember;

class CmMemberRequestController extends Controller
{

    public function __construct(private CmMemberRequestInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmMemberRequestResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmMemberRequestResource($model));
    }



    public function create(CmMemberRequestRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());
        return responseJson(200, 'success', new CmMemberRequestResource($model));
    }

    public function update(CmMemberRequestRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request->validated(), $id);

        return responseJson(200, 'success', new CmMemberRequestResource($model));
    }




    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }

        $relationsWithChildren = $model->hasChildren();

        if (!empty($relationsWithChildren)) {
            $errorMessages = [];
            foreach ($relationsWithChildren as $relation) {
                $relationName = $this->getRelationDisplayName($relation['relation']);
                $childCount = $relation['count'];
                $childIds = implode(', ', $relation['ids']);
                $errorMessages[] = [
                    "message" => "This item has {$childCount} {$relationName} (Names: {$childIds}) and can't be deleted. Remove its {$relationName} first."
                ];
            }
            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null
            ], 400);
        }

        $model->delete();
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        $itemsWithRelations = [];

        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);

            $relationsWithChildren = $model->hasChildren();
            if (!empty($relationsWithChildren)) {
                $itemsWithRelations[] = [
                    'id' => $id,
                    'relations' => $relationsWithChildren,
                ];
                continue;
            }

            $model->delete();
        }

        if (count($itemsWithRelations) > 0) {
            $errorMessages = [];
            foreach ($itemsWithRelations as $item) {
                $itemId = $item['id'];
                $relations = $item['relations'];

                $relationErrorMessages = [];
                foreach ($relations as $relation) {
                    $relationName = $this->getRelationDisplayName($relation['relation']);
                    $childCount = $relation['count'];
                    $childIds = implode(', ', $relation['ids']);
                    $relationErrorMessages[] = [
                        'message' => "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first."
                    ];
                }

                $errorMessages = array_merge($errorMessages, $relationErrorMessages);
            }

            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null
            ], 400);
        }

        return responseJson(200, 'success');
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
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



    public function checkNationalId(Request $request)
    {
        return $this->modelInterface->checkNationalId($request);
    }

    public function getNamesMembers(Request $request)
    {
        if ($request->active) {
            $data = CmMember::select('id', 'full_name')->whereNull('sponsor_id')->paginate(10);

            if ($request->has('search') && $request->search !== '') {
                $data = CmMember::select('id', 'full_name')->whereNull('sponsor_id')->where('full_name', 'like', '%' . $request->search . '%')->paginate(10);
            }
        } else {
            $data = CmMemberRequest::select('id', 'full_name')->get();
        }
        return responseJson(200, "success", $data);
    }
}
