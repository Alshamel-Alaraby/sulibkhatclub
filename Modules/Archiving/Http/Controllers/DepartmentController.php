<?php

namespace Modules\Archiving\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Archiving\Entities\DocumentField;
use Modules\Archiving\Http\Requests\DepartmentRequest;
use Modules\Archiving\Repositories\DepartmentInterface;
use Modules\Archiving\Transformers\DepartmentDocTypeResource;
use Modules\Archiving\Transformers\DepartmentResource;
use Modules\Archiving\Transformers\NewDepartmentResource;

class DepartmentController extends Controller
{

    public function __construct(private DepartmentInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', NewDepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function parentDepartment(Request $request){
        $models = $this->modelInterface->parentDepartment($request);
        return responseJson(200, 'success', DepartmentDocTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function tree(Request $request)
    {
        $models = $this->modelInterface->tree($request);
        return responseJson(200, 'success', DepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function onlyHasKey(Request $request)
    {
        $models = $this->modelInterface->onlyHasKey($request);
        return responseJson(200, 'success', DepartmentResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DepartmentRequest $request)
    {
        $model = $this->modelInterface->create($request);

        return responseJson(200, 'success', new NewDepartmentResource($model));
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
    public function update(DepartmentRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new NewDepartmentResource($model));
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }
}
