<?php

namespace App\Http\Controllers\DocumentHeader;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentHeader\DocumentHeaderRequest;
use App\Http\Requests\DocumentHeader\RenewContractDocumentHeaderRequest;
use App\Http\Resources\DocumentHeader\AllDocumentHeaderResource;
use App\Http\Resources\DocumentHeader\DocumentHeaderResource;
use App\Http\Resources\DocumentHeader\FindDocumentHeaderResource;
use App\Http\Resources\DocumentHeader\HMSDocumentHeaderResource;
use App\Http\Resources\DocumentHeader\NewDocumentHeaderResource;
use App\Http\Resources\DocumentHeader\RealEstateDocumentHeaderResource;
use Illuminate\Http\Request;


class DocumentHeaderController extends Controller
{
    private $resource_class_name;
    public function __construct(private \App\Repositories\DocumentHeader\DocumentHeaderInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
        $this->resource_class_name = request()->module_name == 'hms' ? HMSDocumentHeaderResource::class : NewDocumentHeaderResource::class;

    }

    public function find($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new FindDocumentHeaderResource($model));
    }


    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);
        return responseJson(200, 'success', $this->resource_class_name::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function allDocumentHeader(Request $request)
    {
        $models = $this->modelInterface->allDocumentHeader($request);
        return responseJson(200, 'success', AllDocumentHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }




    public function create(DocumentHeaderRequest $request)
    {
        $model = $this->modelInterface->create($request->validated());

        if ($model == 'false'){
            return responseJson(400, "Not Found Date in Table Financial Year");
        }
        $model->refresh();
        $serials = generalSerial($model);
        $model->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);
        foreach ($model->documentHeaderDetails as $detail){

            foreach ($detail->itemBreakDowns as $break){
                $break->update([
                    "serial_number" => $serials['prefix'],
                ]);
            }

        }
        return responseJson(200, 'success', new DocumentHeaderResource($model));
    }



    public function update(DocumentHeaderRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson( 404 , __('message.data not found'));
        }
        $model_Interface = $this->modelInterface->update($request->validated(),$id);
        if ($model_Interface == 'paid_befor'){
            return responseJson(402, "You can't edit because there is a settlement for this invoice");
        }
        if ($model_Interface == 'false'){
            return responseJson(400, "Not Found Date in Table Financial Year");
        }
        $model->refresh();
        return responseJson(200, 'success', new DocumentHeaderResource($model));

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
//        if ($model->hasChildren()){
//            return responseJson(400, __("this item has children and can't be deleted remove it's children first"));
//        }

        $this->modelInterface->delete($id);
        return responseJson(200, 'success');
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $this->modelInterface->delete($id);
        }
        return responseJson(200, __('Done'));
    }



    private function getRelationDisplayName($relation)
    {
        $displayableName = str_replace('_', ' ', $relation);
        return ucwords($displayableName);
    }


    public function checkDateModelFinancialYear(Request $request){
        if (generalCheckDateModelFinancialYear($request['date']) == "true"){
            return "true";
        }
        return "false";
    }
    public function getDateRelatedDocumentId(Request $request){
        $models = $this->modelInterface->getDateRelatedDocumentId($request);
        return responseJson(200, 'success', DocumentHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function checkBooking()
    {
        $models = $this->modelInterface->checkBooking();
        return $models;
    }

    public function getDocumentsCustomer($id,Request $request)
    {
        $data = $this->modelInterface->getDocumentsCustomer($id,$request);
        return $data;
    }

    public function getCheckOutPrint($id)
    {
        return $data = $this->modelInterface->checkOutPrint($id);
    }

    public function getCustomerRoom(Request $request)
    {
        return $data = $this->modelInterface->customerRoom($request);
    }

    public function getCheckInCustomer()
    {
        return $data = $this->modelInterface->checkInCustomer();

    }

    public function createDailyCheckInCustomer()
    {
        return $data = $this->modelInterface->createDailyCheckInCustomer();

    }

    public function updateCheckInCustomer(Request $request)
    {
        return $data = $this->modelInterface->updateCheckInCustomer($request);

    }

     public function createDailyInvoiceOnline(Request $request)
    {
        return $data = $this->modelInterface->createDailyInvoiceOnline($request);

    }

    public function getDocumentRealEstateData($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new RealEstateDocumentHeaderResource($model));
    }

    public function updateContractHeader(DocumentHeaderRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson( 404 , __('message.data not found'));
        }
        $model_Interface = $this->modelInterface->updateContractHeader($request,$id);
        if ($model_Interface == 'paid_befor'){
            return responseJson(402, "You can't edit because there is a settlement for this invoice");
        }
        if ($model_Interface == 'false'){
            return responseJson(400, "Not Found Date in Table Financial Year");
        }
        $model->refresh();
        return responseJson(200, 'success', new DocumentHeaderResource($model));

    }


    public function all_renew_contract_header(Request $request)
    {
        $models = $this->modelInterface->all_renew_contract_header($request);
        return responseJson(200, 'success', RealEstateDocumentHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function renew_contract(RenewContractDocumentHeaderRequest $request)
    {
        $this->modelInterface->renew_contract($request);
    }


}
