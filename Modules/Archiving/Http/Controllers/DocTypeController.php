<?php

namespace Modules\Archiving\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Archiving\Entities\DocType;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Archiving\Http\Requests\DocTypeRequest;
use Modules\Archiving\Transformers\DocTypeResource;
use Modules\Archiving\Repositories\DocTypeInterface;
use Modules\Archiving\Transformers\AllDocTypeResource;
use Modules\Archiving\Transformers\NewDocTypeResource;
use Modules\Archiving\Transformers\DocTypeTreeResource;

class DocTypeController extends Controller
{
    public function __construct(private DocTypeInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {

        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', AllDocTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        return responseJson(200, 'success', new AllDocTypeResource($model));
    }

    public function create(DocTypeRequest $request)
    {
        $model = $this->modelInterface->create($request);
        $model->refresh();
        return responseJson(200, 'success', new AllDocTypeResource($model));

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
    public function update(DocTypeRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, 'data not found');
        }
        $this->modelInterface->update($request, $id);
        $model->refresh();
        return responseJson(200, 'success', new AllDocTypeResource($model));
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

    // tree of doc types
    public function tree()
    {
        $models = $this->modelInterface->tree();
        return responseJson(200, 'success', DocTypeTreeResource::collection($models));
    }
    public function nodesLevelTwo(Request $request)
    {
        $models = $this->modelInterface->nodesLevelTwo($request);
        return responseJson(200, 'success', \Modules\Archiving\Transformers\DocRelationResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
    public function addStatusToDocumentType(Request $request)
    {
        if ($this->modelInterface->documentStatusExist($request->status_id, $request->doc_type_id)) {
            return response()->json(["error" => "Document status exist"], 422);
        }
        $this->modelInterface->addStatusToDocumentType($request);
    }

    public function removeStatusFromDocumentType($doc_type_id, $status_id)
    {
        $this->modelInterface->removeStatusFromDocumentType($doc_type_id, $status_id);
    }

    public function getDocTypeStatuses($doc_type_id)
    {
        return DocType::find($doc_type_id)->statuses;
    }
}
