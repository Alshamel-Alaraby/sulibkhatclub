<?php

namespace Modules\RealEstate\Http\Controllers;

use App\Models\Document;
use App\Models\Serial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\RealEstate\Entities\RlstContractHeader;
use Modules\RealEstate\Entities\RlstContractHeaderDetail;
use Modules\RealEstate\Entities\RlstContractHeaderUnit;
use Modules\RealEstate\Http\Requests\RlstContractHeaderRequest;
use Modules\RealEstate\Transformers\RlstContractHeaderResource;
use Modules\RecievablePayable\Entities\RpBreakDown;
use Modules\RecievablePayable\Entities\RpInstallmentPaymentType;

class RlstContractHeaderController extends Controller
{
    public function __construct(private RlstContractHeader $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->data()->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        if ($request->document_id) {
            $models->where('document_id', $request->document_id);
        }

        if ($request->start_date && $request->end_date) {
            $models->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('date', ">=", $request->start_date)
                        ->whereDate('date', "<=", $request->end_date);
                });
            });
        }

        if ($request->start_serial_number && $request->end_serial_number) {
            $models->where('serial_number', ">=", $request->start_serial_number)->where('serial_number', "<=", $request->end_serial_number);
        }

        if ($request->is_related_document) {
            $document = Document::find($request->document_id);
            $documentHeaderUnsetId = $document->documentRelatedHeader->where('related_document_number', '!=', null)->pluck('related_document_number')->toArray();
            if ($request->is_related_document == 1) {
                $models->whereIn('id', $documentHeaderUnsetId);
            } elseif ($request->is_related_document == 2) {
                $models->whereNotIn('id', $documentHeaderUnsetId);
            }
        }

        return responseJson(200, 'success', RlstContractHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->model->data()->find($id);

        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new RlstContractHeaderResource($model));
    }

    public function create(RlstContractHeaderRequest $request)
    {
//        return $request['units']['unit_id'];


        $serial = Serial::where([['branch_id', $request['branch_id']], ['document_id', $request['document_id']]])->first();
        $request['serial_id'] = $serial ? $serial->id : null;
        $model = $this->model->create(array_merge($request->validated(), ['serial_id' => $request['serial_id']]));

        if (isset($request['units'])) {
            foreach ($request['units'] as $unit) {
                $model->units()->attach($unit['unit_id'], ['unit_services' => collect($unit['unit_services'])]);
            }
        }

        if (isset($request['header_details'])) {
            foreach ($request['header_details'] as $detail) {
                $contractHeaderDetail = RlstContractHeaderDetail::create(array_merge($detail, ['contract_header_id' => $model->id]));
                $this->createBarekDown($contractHeaderDetail, $model, $request['units']);

            }
        }

        $serials = generalSerial($model);
        $model->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);

        return responseJson(200, 'created', new RlstContractHeaderResource($model));

    }
    public function CalculateDate($break_downs)
    {
        $PaymentType = RpInstallmentPaymentType::find($break_downs['payment_type_id']);

        $date_instalment = \Carbon\Carbon::parse($break_downs['from_date']);

        if (strlen($break_downs['due_day']) == 1) {
            $date_old = Carbon::parse($break_downs['from_date'])->format('Y-m') . '-0' . $break_downs['due_day'];
            $date_old_end = $break_downs['to_date'];
            $date_instalment = \Carbon\Carbon::parse($date_old);

        } else {
            $date_old = Carbon::parse($break_downs['from_date'])->format('Y-m') . '-' . $break_downs['due_day'];
            $date_old_end = $break_downs['to_date'];
            $date_instalment = \Carbon\Carbon::parse($date_old);
        }


        $cont_date = 0;
        $array_date = [];

        $i = 0;
        $x = 1;

        for ($i; $i < $x; $i++) {

            if ($i == 0) {

                $date = $date_old;
            } else {
                $cont_date = $PaymentType->freq_period;

                if ($PaymentType->step == "D") {
                    $date = $date_instalment->addDays($cont_date)->format('Y-m-d');
                }

                if ($PaymentType->step == "M") {

                    $date = $date_instalment->addMonths($cont_date)->format('Y-m-d');
                }

                if ($PaymentType->step == "Y") {

                    $date = $date_instalment->addYears($cont_date)->format('Y-m-d');
                }
            }


            if ($date_old_end > $date) {

                $x++;
                $array_date['date'][$i] = $date;
            }

        }
        return $array_date;


    }


    public function createBarekDown($contractHeaderDetail, $data, $units)
    {

        $array = $this->CalculateDate($contractHeaderDetail);

        foreach ($array['date'] as $object_date) {

            RpBreakDown::create([
                'instalment_date' => $object_date,
                'rate' => 1,
                'repate' => 1,
                'currency_id' => 1,
                'document_id' => $data['document_id'],
                'customer_id' => $data['tenant_id'],
                'break_id' => $data['id'],
                'instalment_type_id' => 1,
                'break_type' => 'contractHeader',
                'debit' => ($data->document->attributes && $data->document->attributes['customer'] == 1) ? $contractHeaderDetail['rent_amount'] : 0,
                'credit' => ($data->document->attributes && $data->document->attributes['customer'] == -1) ? $contractHeaderDetail['rent_amount'] : 0,
                'total' => $contractHeaderDetail['rent_amount'],
                'installment_statu_id' => 1,
                'client_type_id' => 7,
                'details' => "contract_header_detail",
            ]);

//            $total_unit_services = RlstUnitService::whereIn('unit_id', $units)->whereDate('from_date', '>=', $contractHeaderDetail['from_date'])->whereDate('from_date', '<=', $contractHeaderDetail['to_date'])->sum('default_price');
//            if ($total_unit_services) {
//
//                RpBreakDown::create([
//                    'instalment_date' => $object_date,
//                    'rate' => 1,
//                    'repate' => 1,
//                    'currency_id' => 1,
//                    'document_id' => $data['document_id'],
//                    'customer_id' => $data['tenant_id'],
//                    'break_id' => $data['id'],
//                    'instalment_type_id' => 1,
//                    'break_type' => 'contractHeader',
//                    'debit' => ($data->document->attributes && $data->document->attributes['customer'] == 1) ? $total_unit_services : 0,
//                    'credit' => ($data->document->attributes && $data->document->attributes['customer'] == -1) ? $total_unit_services : 0,
//                    'total' => $total_unit_services,
//                    'installment_statu_id' => 1,
//                    'client_type_id' => $data['tenant_id'],
//                    'details' => "unit_services",
//                ]);


//            }


        }
        RpBreakDown::create([
            'instalment_date'       => $data['date'],
            'rate'                  => 1,
            'repate'                => 1,
            'currency_id'           => 1,
            'document_id'           => $data['document_id'],
            'customer_id'           => $data['tenant_id'],
            'break_id'              => $data['id'],
            'instalment_type_id'    => 1,
            'break_type'            => 'contractHeader',
            'debit'                 => ($data->document->attributes && $data->document->attributes['customer'] == 1)?$data['insurance_amount']:0,
            'credit'                => ($data->document->attributes && $data->document->attributes['customer'] == -1)?$data['insurance_amount']:0,
            'total'                 => $data['insurance_amount'],
            'installment_statu_id'  => 1,
            'client_type_id'        => 7,
            'details'               => "insurance_amount",
        ]);


    }





    public function update($id, RlstContractHeaderRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        if ($request['breakSettlement'] == 1){

            $model->update($request->validated());
            $model->refresh();
            return responseJson(200, 'updated', new RlstContractHeaderResource($model));

        }else{

            if ($model->rpBreakDowns){
                foreach ($model->rpBreakDowns as $down){
                    if ($down->breakSettlements){
                        foreach ($down->breakSettlements as $settlement){
                            $settlement->delete();
                        }
                    }
                    $down->delete();
                }
            }
            if ($model->contractHeaderDetail){
                foreach ($model->contractHeaderDetail as $detail){
                    $detail->delete();
                }
            }
            if ($model->contractHeaderDetail){
                foreach ($model->contractHeaderDetail as $detail){
                    $detail->delete();
                }
            }
            if ($model->units){
                foreach ($model->units as $unit){
                    $header_unit = RlstContractHeaderUnit::find($unit['pivot']['id']);
                    $header_unit->delete();
                }
            }
            $model->delete();
            $model_new = $this->create($request);
            return $model_new;

        }


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

//    public function delete($id)
//    {
//        $model = $this->model->find($id);
//
//        if (!$model) {
//            return responseJson(404, 'not found');
//        }
//
//        $model->delete();
//        $model->refresh();
//
//        return responseJson(200, 'success');
//    }
//
//    public function bulkDelete(Request $request)
//    {
//
//        foreach ($request->ids as $id) {
//            $model = $this->model->find($id);
//            $model->delete();
//            $model->refresh();
//
//        }
//
//        return responseJson(200, 'deleted');
//    }

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
                        'message' => "Item with ID {$itemId} has {$childCount} {$relationName} (IDs: {$childIds}) and can't be deleted. Remove its {$relationName} first.",
                    ];
                }

                $errorMessages = array_merge($errorMessages, $relationErrorMessages);
            }

            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null,
            ], 400);
        }

        return responseJson(200, 'success');
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
                    "message" => "This item has {$childCount} {$relationName} (Names: {$childIds}) and can't be deleted. Remove its {$relationName} first.",
                ];
            }
            return response()->json([
                "message" => $errorMessages,
                "data" => null,
                "pagination" => null,
            ], 400);
        }

        $model->delete();
        return responseJson(200, 'success');
    }

    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }

}
