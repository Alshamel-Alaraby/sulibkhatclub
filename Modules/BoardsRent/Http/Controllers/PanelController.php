<?php

namespace Modules\BoardsRent\Http\Controllers;

use App\Http\Requests\AllRequest;
use App\Http\Resources\AllDropListResource;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\BoardsRent\Entities\Panel;
use Modules\BoardsRent\Http\Requests\PanelRequest;
use Modules\BoardsRent\Transformers\Panel\AllBRentPanelResource;
use Modules\BoardsRent\Transformers\PanelResource;
use Modules\BoardsRent\Transformers\Panel\RelationBRentPanelResource;

class PanelController extends Controller
{
    public function __construct(private Panel $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new AllBRentPanelResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', AllBRentPanelResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
    public function searchDropDown(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', RelationBRentPanelResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(PanelRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();

        return responseJson(200, 'created', new AllBRentPanelResource($model));

    }

    public function update($id, PanelRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();

        return responseJson(200, 'updated', new AllBRentPanelResource($model));
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function delete($id)
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
    public function getFilterPanel(Request $request)
    {

        $models_id = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
        ->where('face','!=','Multi')->whereHas('itemBreakDowns',function ($q) use ($request){
                $q->whereDate('date_from','<=',$request->date_to)->whereDate('date_to','>=',$request->date_from);
            })->whereHas('itemBreakDowns',function ($q){
                $q->whereHas('documentHeaderDetail',function ($q){
                    $q->whereHas('documentHeader',function ($q){
                        $q->whereHas('document',function ($q){
                            $q->where('attributes->item_quantity',"-1");
                        });
                    });
                });
            })->pluck('id')->toArray();

        $models = $this->model->filter($request)->data()->where('is_active',$request->is_active)->whereNotIn('id',$models_id);
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', PanelResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getReportPanel()
    {
        $models  = $this->model->all();
        return $models;
//        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

    }


    public function getDropDown(Request $request)
    {
        $models = $this->model->getName($request);
        return responseJson(200, 'success', AllDropListResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }
}
