<?php

namespace App\Http\Controllers\DocumentHeaderDetail;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentHeaderDetail\DocumentHeaderDetailRequest;
use App\Http\Resources\DocumentHeaderDetail\DocumentHeaderDetailReportResource;
use App\Http\Resources\DocumentHeaderDetail\DocumentHeaderDetailResource;
use App\Http\Resources\PanelUsageRateReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Booking\Transformers\Unit\RelationBookingUnitResource;
use Modules\Booking\Transformers\UnitResource;

class DocumentHeaderDetailController extends Controller
{
    public function __construct(private \App\Repositories\DocumentHeaderDetail\DocumentHeaderDetailInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new DocumentHeaderDetailResource($model));
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', DocumentHeaderDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(DocumentHeaderDetailRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());

        return responseJson(200, 'success', new DocumentHeaderDetailResource($model));
    }

    public function update(DocumentHeaderDetailRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $this->modelInterface->update($request->validated(), $id);
        $model->refresh();
        return responseJson(200, 'success', new DocumentHeaderDetailResource($model));

    }
    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
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
                    "message" => "This item has {$childCount} {$relationName} (serial numbers: {$childIds}) and can't be deleted. Remove its {$relationName} first."
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
                        'message' => "Item with ID {$itemId} has {$childCount} {$relationName} (serial numbers: {$childIds}) and can't be deleted. Remove its {$relationName} first."
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

    public function reportDocument(Request $request)
    {
        $models = $this->modelInterface->getReportDocument($request);
        return responseJson(200, 'success', $models);
    }

    public function annualFinancialReport(Request $request)
    {
        $models = $this->modelInterface->getAnnualFinancialReport($request);
        return responseJson(200, 'success', $models);
    }

    public function annualFinancialReportDetail(Request $request)
    {
        $models = $this->modelInterface->getAnnualFinancialReportDetail($request);
        return responseJson(200, 'success', $models);

    }
    public function panelUsageRateReport(Request $request)
    {
        $models = $this->modelInterface->getPanelUsageRateReport($request);
//        return responseJson(200, 'success', $models);
        return responseJson(200, 'success', PanelUsageRateReportResource::collection($models));

    }

    public function getRooms(Request $request)
    {
        $models = $this->modelInterface->getRooms($request);

        return responseJson(200, 'success', UnitResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function getBookingReport(Request $request)
    {
        $models = $this->modelInterface->allBookingReport($request);

        return responseJson(200, 'success',  DocumentHeaderDetailReportResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }
}
