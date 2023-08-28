<?php


namespace App\Repositories\DocumentHeader;
use App\Http\Resources\Document\DocumentResource;
use App\Models\AttendantDocumentHeader;
use App\Models\Document;
use App\Models\DocumentHeaderDetail;
use App\Models\GeneralCustomTable;
use App\Models\ItemBreakDown;
use App\Models\Serial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Booking\Entities\Setting;
use Modules\RecievablePayable\Entities\RpBreakDown;

class DocumentHeaderRepository implements DocumentHeaderInterface
{
    public function __construct(private \App\Models\DocumentHeader $model ,private DocumentHeaderDetail $modelDetail ,private ItemBreakDown $modelBreak  )
    {
        $this->model = $model;
        $this->modelDetail = $modelDetail;
        $this->modelBreak = $modelBreak;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if($request->document_id)
        {
            $models->where('document_id',$request->document_id);
        }

        if($request->start_date && $request->end_date)
        {
            $models->where(function ($q) use ($request) {
                $q->when($request->start_date && $request->end_date, function ($q) use ($request) {
                    $q->whereDate('date', ">=", $request->start_date)
                        ->whereDate('date', "<=", $request->end_date);
                });
            });
        }

        if($request->start_serial_number && $request->end_serial_number)
        {
            $models->where('serial_number', ">=", $request->start_serial_number)->where('serial_number', "<=", $request->end_serial_number);
        }

        if($request->is_related_document)
        {
            $document = Document::find($request->document_id);
            $documentHeaderUnsetId = $document->documentRelatedHeader->where('related_document_number','!=',null)->pluck('related_document_number')->toArray();
            if ($request->is_related_document == 1)
            {
                $models->whereIn('id',$documentHeaderUnsetId);
            }elseif ($request->is_related_document == 2)
            {
                $models->whereNotIn('id',$documentHeaderUnsetId);
            }
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function allDocumentHeader($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function getDateRelatedDocumentId($request)
    {
        $document = Document::find($request->related_document_id);
        $documentHeaderUnsetId = $document->documentRelatedHeader->where('document_id',$request->document_id)->where('related_document_id',$request->related_document_id)->pluck('related_document_number')->toArray();
        if ($document->is_copy == 1){
            $models = $this->model->filter($request)->
            orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
                ->where('document_id',$request->related_document_id)
                ->whereNotIn('id',$documentHeaderUnsetId)
                ->where('branch_id',$request->branch_id);
        }
        if ($document->is_copy == 0){
            $models = $this->model->filter($request)->
            orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
                ->where('document_id',$request->related_document_id)->where('complete_status','UnDelivered')
                ->where('branch_id',$request->branch_id);
        }
        if ($document->need_approve == 1){
            $models->where('document_status_id',2);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }


    public function find($id)
    {
        $data = $this->model->find($id);
        return $data;
    }
    public function generalDocument($request,$header_details){
        $document = Document::find($request['document_id']);
        if ($document){
            $data = [];
            $data['invoice_discount'] = 0;
            $data['net_invoice'] =0;
            $data['sell_method_discount'] =0;
            $data['unrealized_revenue'] =0;
            $data['external_commission'] =0;

            if ($request['total_invoice'] !=0) {
                $data['invoice_discount'] = ($request['invoice_discount'] * $header_details['total']) / $request['total_invoice'];
                $data['net_invoice'] = ($request['net_invoice'] * $header_details['total']) / $request['total_invoice'];
                if ($request['sell_method_id']){
                    $data['sell_method_discount']   = $request['sell_method_id'] != 1 ? ($request['sell_method_discount']  * $header_details['total']) / $request['total_invoice'] : $header_details['sell_method_discount'] ;
                }
                $data['unrealized_revenue']        = ( ($request['unrelaized_revenue']  * $header_details['total']) / $request['total_invoice']) * (isset($document->attributes['unrealized_revenue'])?$document->attributes['unrealized_revenue']:0);
                if (isset($request['external_commission']))
                {
                    $data['external_commission']       = ( ($request['external_commission'] * $header_details['total']) / $request['total_invoice']) * (isset($document->attributes['commission'])?$document->attributes['commission']:0);
                }
            }

            return  $data;
        }
        return 'false';

    }


    public function create($request){

        return DB::transaction(function () use ($request) {

            if (generalCheckDateModelFinancialYear($request['date']) == "true"){

                $data_request = $request;
                $serial = Serial::where([['branch_id',$request['branch_id']],['document_id',$request['document_id']]])->first();
                $data_request['serial_id'] = $serial ? $serial->id:null;
                $data = $this->model->create($data_request);
                if (isset($request['attendants'])){
                    $data->attendants()->attach($request['attendants']);
                }

                if (isset($data_request['related_document_number'])){
                    $model_find =  $this->find($data_request['related_document_number']);
                    if ($model_find){
                        $model_find->update([
                            "complete_status" => 'Delivered'
                        ]);
                    }
                }
                foreach ($request['header_details'] as $header_details ){

                    $general_document = $this->generalDocument($request,$header_details);

                    $id =  $this->modelDetail->create(array_merge($header_details,[
                        'document_header_id'   =>$data->id,
                        'invoice_discount'     =>$general_document['invoice_discount'],
                        'net_invoice'          =>$general_document['net_invoice'],
                        'sell_method_discount' =>$general_document['sell_method_discount'],
                        'unrealized_revenue'   =>$general_document['unrealized_revenue'],
                        'external_commission'  =>$general_document['external_commission'],
                    ]));
                    if (isset($header_details['break_downs']))
                    {
                        foreach ($header_details['break_downs'] as $break){
                            $this->modelBreak->create(array_merge($break,['document_header_detail_id'=>$id->id]));
                        }
                    }
                    if (isset($header_details['prefix_related'])){
                        $model_find =  $this->model->where('prefix',$header_details['prefix_related'])->first();
                        if ($model_find){
                            $model_find->update([
                                "complete_status" => 'Delivered',
                                "related_document_id" => $model_find['document_id']??$data['document_id'],
                                "related_document_number" => $model_find['id']??$data['id'],
                                "related_document_prefix" => $model_find['serial_number']??$data['serial_number'],
                            ]);
                        }
                    }
                }
                if ($request['is_break'] == 1)
                {
                    RpBreakDown::create([
                        'instalment_date' => $request['date'],
                        'rate' => 1,
                        'repate' => 1,
                        'currency_id' => 1,
                        'document_id' => $request['document_id'],
                        'customer_id' => $request['customer_id'],
                        'break_id' => $data['id'],
                        'instalment_type_id' => 1,
                        'break_type' => 'documentHeader',
                        'debit' => ($data->document->attributes && $data->document->attributes['customer'] == 1)?$data['net_invoice']:0,
                        'credit' => ($data->document->attributes && $data->document->attributes['customer'] == -1)?$data['net_invoice']:0,
                        'total' =>$data['net_invoice'],
                        'installment_statu_id' =>1,
                    ]);
                }
                return $data;
            }
            return 'false';
        });
    }


    public function update($request,$id){
        if (generalCheckDateModelFinancialYear($request['date']) == "true"){
                $data = $this->model->find($id);
                if (isset($request['related_document_number'])){
                    $related_document_data  = $this->model->find($data->related_document_number);
                    if ($related_document_data){
                        $related_document_data->update([
                            "complete_status" => 'UnDelivered'
                        ]);
                    }
                    $related_document_number  = $this->model->find($request['related_document_number']);
                    $related_document_number->update([
                        "complete_status" => 'Delivered'
                    ]);
                }
                $data->update($request);
                if (isset($request['attendants'])){
                    $data->attendants()->sync($request['attendants']);
                }
                if(isset($request['header_details'])){
                    if ($data->documentHeaderDetails){
                        foreach ($data->documentHeaderDetails as $Details){
                            foreach ($Details->itemBreakDowns as $break){
                                $break->delete();
                            }
                            $Details->delete();
                        }
                    }
                    foreach ($request['header_details'] as $header_details ){
                        $general_document = $this->generalDocument($request,$header_details);

                        $id =  $this->modelDetail->create(array_merge($header_details,[
                            'document_header_id'   =>$data->id,
                            'invoice_discount'     =>$general_document['invoice_discount'],
                            'net_invoice'          =>$general_document['net_invoice'],
                            'sell_method_discount' =>$general_document['sell_method_discount'],
                            'unrealized_revenue'   =>$general_document['unrealized_revenue'],
                            'external_commission'  =>$general_document['external_commission'],
                        ]));
                        if (isset($header_details['prefix_related'])){
                            $model_find =  $this->model->where('prefix',$header_details['prefix_related'])->first();
                            if ($model_find){
                                $model_find->update([
                                    "complete_status" => 'Delivered',
                                    "related_document_id" => $model_find['document_id']??$data['document_id'],
                                    "related_document_number" => $model_find['id']??$data['id'],
                                    "related_document_prefix" => $model_find['serial_number']??$data['serial_number'],
                                ]);
                            }
                        }
                        if (isset($header_details['break_downs'])) {
                            foreach ($header_details['break_downs'] as $break) {
                                $this->modelBreak->create(array_merge($break, ['document_header_detail_id' => $id->id, 'serial_number' => $data->prefix]));
                            }
                        }
                    }
                }
                return $data;
        }
        return 'false';


    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id){

        $model = $this->model->find($id);
        if ($model->documentHeaderDetails){
            foreach ($model->documentHeaderDetails as $Details){
                $prefix_related = $this->model->where('prefix',$Details['prefix_related'])->first();
                if ($prefix_related){
                    $prefix_related->update([
                        "complete_status" => 'UnDelivered',
                        "related_document_id" => $prefix_related['related_document_id'] == 33 ?$prefix_related['related_document_id']:null,
                        "related_document_number" => $prefix_related['related_document_id'] == 33 ?$prefix_related['related_document_number']:null,
                        "related_document_prefix" => $prefix_related['related_document_id'] == 33 ?$prefix_related['related_document_prefix']:null,
                    ]);
                }
                foreach ($Details->itemBreakDowns as $break){
                    $break->delete();
                }
                $Details->delete();
            }
        }
        $model->delete();
    }

    public function checkBooking()
    {
        return DB::transaction(function () {

            // object Check_Out Booking
            $booking = Setting::find(2);

            // check Time Setting  == || >=   Time now
            if ($booking->value >= now()->format('H:i:s')){

                // check in model DocumentHeader -> document_id == 33 &&  related_document_id == null all()
                $models_document_header = $this->model->with('documentHeaderDetails')->where('document_id',33)->whereNull('related_document_id')->get();
                foreach ($models_document_header as   $model){
                    $check_header_details_date = $model->documentHeaderDetails()->whereDate('date_to','>=',now()->format('Y-m-d'))->get();
                    if ($check_header_details_date->count() > 0)
                    {
                        $columns_create_document_header =  collect($model)->except(['id','complete_status','deleted_at','created_at','updated_at','date','related_document_id','related_document_prefix','related_document_number','serial_number','prefix','document_id','serial_id']);


                        $serial = Serial::where([['branch_id',$model['branch_id']],['document_id',35]])->first();
                        $serial_id = $serial ? $serial->id:null;
                        // Create Document Header
                        $document_header = $this->model->create(array_merge($columns_create_document_header->all(),[
                            'date' => now()->format('Y-m-d'),
                            'document_id'         => 35,
                            'related_document_id' => 33,
                            'related_document_number' => $model['id'],
                            'related_document_prefix' => $model['serial_number'],
                            'serial_id'               => $serial_id,
                        ]));
                        if ($model->attendants){
                            $document_header->attendants()->attach($model->attendants->pluck('id')->toArray());
                        }

                        $document_header->refresh();

                        // Create Serial
                        $this->createSerial($document_header);

                        // Create Document Header Detail
                        $this->createBookingDocumentHeaderDetail($model,$document_header);
                    }

                }
                return responseJson(200, "Success");

            }
            return responseJson(400, "Time Expires");

        });
    }

    public function createSerial($model_header){

        $serials = generalSerial($model_header);
        $model_header->update([
            "serial_number" => $serials['serial_number'],
            "prefix" => $serials['prefix'],
        ]);
    }

    function createBookingDocumentHeaderDetail($model,$model_header){

        // columns used update 3 columns in  Document Header
        $sum_discount    = 0;
        $sum_total       = 0;
        $sum_net_invoice = 0;

        // check in relationship documentHeaderDetails  Exists in documentHeader
        if ($model->documentHeaderDetails){

            // check in relationship documentHeaderDetails    date_to  == || >  date now
            $model_header_details = $model->documentHeaderDetails()->whereDate('date_to','>=',now()->format('Y-m-d'))->get();

            foreach ($model_header_details as $HeaderDetails):

                $item_discount = $HeaderDetails['discount']  /  $HeaderDetails['rent_days'];
                $sum_discount += $item_discount;

                $total        = $HeaderDetails['price_per_uint']  *  1 ;
                $sum_total   += $total;

                $net_invoice      = $total  -  $item_discount  ;
                $sum_net_invoice += $net_invoice;

                $this->modelDetail->create([
                    'document_header_id' => $model_header->id,
                    'date_from'          => now()->format('Y-m-d'),
                    'date_to'            => now()->format('Y-m-d'),
                    'check_in_time'      => now()->format('H:i'),
                    'rent_days'          => 1,
                    'quantity'           => 1,
                    'discount'           => $item_discount,
                    'total'              => $total ,
                    'invoice_discount'   => $item_discount ,
                    'net_invoice'        => $total  -  $item_discount  ,
                    'unit_type'          => $HeaderDetails['unit_type'] ,
                    'price_per_uint'     => $HeaderDetails['price_per_uint'] ,
                    'unit_id'            => $HeaderDetails['unit_id'],
                ]);
            endforeach;
        }

        $model_header->update([
            "total_invoice"    => $sum_total,
            "invoice_discount" => $sum_discount,
            "net_invoice"      => $sum_net_invoice,
        ]);

    }

    public function getDocumentsCustomer($id)
    {
        $documentIgnore = [33,34];
        $data = [];
        $data['details'] = $this->modelDetail->with(['unit','documentHeader'])->where('unit_type','Booking')->whereHas('documentHeader',function ($q) use ($id,$documentIgnore){
            $q->where('customer_id',$id)
                ->where('complete_status','UnDelivered')
                ->whereNotIn('document_id',$documentIgnore);
        })->get();

        $data['header'] = $this->modelDetail->with(['documentHeader'=>function($q){
           $q->with('attendantsDocument');
       }])->where('unit_type','Booking')->whereHas('documentHeader',function ($q) use ($id,$documentIgnore){
           $q->where('customer_id',$id)
               ->where('complete_status','UnDelivered')
               ->where('document_id',35);
       })->first();

        return $data;
    }

}
