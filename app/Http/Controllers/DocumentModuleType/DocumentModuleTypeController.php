<?php

namespace App\Http\Controllers\DocumentModuleType;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentModuleType\DocumentModuleTypeRequest;
use App\Http\Resources\DocumentModuleType\DocumentModuleTypeResource;
use App\Http\Resources\DocumentModuleType\ModuleTypeResource;
use App\Repositories\DocumentModuleType\DocumentModuleTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentModuleTypeController extends Controller
{
    public function __construct( private DocumentModuleTypeInterface $repository)
    {
        $this->repository = $repository;
    }

    public function find($id)
    {

        $model = $this->repository->find($id);
        if ($model) {
            return responseJson(200, 'success', new DocumentModuleTypeResource($model));
        } else {
            return responseJson(404, __('message.data not found'));
        }

    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function all(Request $request)
    {
        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', DocumentModuleTypeResource::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }

    public function create(DocumentModuleTypeRequest $request)
    {
        $model = $this->repository->create($request->validated());
        return responseJson(200, 'success');
    }

    public function update(DocumentModuleTypeRequest $request, $id)
    {
        $model = $this->repository->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->repository->update($request->validated(), $id);

        return responseJson(200, 'success');
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

    public function delete($id)
    {
        $model = $this->repository->find($id);
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
                    "message" => "This item has {$childCount} {$relationName} (IDS: {$childIds}) and can't be deleted. Remove its {$relationName} first."
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
            $model = $this->repository->find($id);

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



    public function getDropDown(Request $request)
    {
        $models = $this->repository->getName($request);
        return responseJson(200, 'success', DocumentModuleTypeResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getDropDawnModel(Request $request)
    {
        $models = DB::table($request->db_table)->whereNull('deleted_at')->get();
        return responseJson(200, 'success', ModuleTypeResource::collection($models));
    }
}
