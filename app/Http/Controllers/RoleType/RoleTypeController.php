<?php

namespace App\Http\Controllers\RoleType;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleType\RoleTypeRequest;
use App\Http\Requests\RoleType\EditRoleTypeRequest;
use App\Http\Resources\RoleTypes\RoleTypeResource;
use App\Models\RoleType;
use App\Repositories\RoleType\RoleTypeRepositoryInterface;
use Illuminate\Http\Request;

class RoleTypeController extends Controller
{
    public $repository;
    public $resource = RoleTypeResource::class;
    public function __construct(RoleTypeRepositoryInterface $repository, private RoleType $model)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     * @return \response
     */
    public function index(Request $request)
    {


        $data = $this->repository->getAll($request);
        return responseJson(200, 'success', ($this->resource)::collection($data['data']), $data['paginate'] ? getPaginates($data['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \response
     */
    public function store(RoleTypeRequest $request)
    {

        $this->repository->create($request->validated());
        return responseJson(200, __('done'));

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \response
     */
    public function show($id)
    {
        if ($data = $this->repository->find($id)) {
            return responseJson(200, __('Done'), new $this->resource($data), 200);
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
    public function update(RoleTypeRequest $request, $id)
    {
        $data = [];

        if ($request->name) {
            $data['name'] = $request->name;
        }

        if ($request->name_e) {
            $data['name_e'] = $request->name_e;
        }

        $this->repository->update($data, $id);
        return responseJson(200, __('updated'));

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
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \response
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);
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
            $model = $this->model->find($id);

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
}
