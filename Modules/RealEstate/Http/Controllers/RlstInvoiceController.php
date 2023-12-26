<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Http\Requests\AllRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstInvoice;
use Modules\RealEstate\Http\Requests\RlstInvoiceRequest;
use Modules\RealEstate\Repositories\RlstInvoice\RlstInvoiceInterface;
use Modules\RealEstate\Repositories\RlstInvoice\RlstInvoiceRepository;
use Modules\RealEstate\Transformers\RlstInvoiceResource;

class RlstInvoiceController extends Controller
{

    public function __construct(private RlstInvoiceInterface $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstInvoiceResource($model));
    }

    public function all(AllRequest $request)
    {

        $models = $this->model->all($request);

        return responseJson(200, 'success', RlstInvoiceResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(RlstInvoiceRequest $request)
    {
        $model = $this->model->create($request);
        $model->refresh();
        $serials = generalSerial($model);
        $model->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);

        return responseJson(200, 'created', new RlstInvoiceResource($model));

    }

    public function update($id, RlstInvoiceRequest $request, RlstInvoiceRepository $repository)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model = $repository->update($id, $request);
        $serials = generalSerialUpdate($model);
        $model->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);
        return responseJson(200, 'updated', new RlstInvoiceResource($model));
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

    public  function  getAll(Request $request)
    {
        $model = RlstInvoice::where(function ($q) use ($request){
            $q->when($request->document_id,function ($q) use ($request){
                $q->where('document_id',$request->document_id);
            });
        })->where(function ($q) use ($request){
            $q->when($request->payment_method_id,function ($q) use ($request){
                $q->where('payment_method_id',$request->payment_method_id);
            });
        })->where(function ($q) use ($request){
                $q->when($request->customer_id,function ($q) use ($request){
                    $q->where('customer_id',$request->customer_id);
                });
        })->get();

        return $model;
    }
}
