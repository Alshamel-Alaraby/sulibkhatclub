<?php


namespace App\Repositories\DocumentHeader;
use App\Http\Resources\Document\DocumentResource;
use App\Models\DocumentHeaderDetail;
use App\Models\GeneralCustomTable;
use App\Models\ItemBreakDown;
use App\Models\Serial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;
class DocumentHeaderRepository implements DocumentHeaderInterface
{
    public function __construct(private \App\Models\DocumentHeader$model , DocumentHeaderDetail $modelDetail ,ItemBreakDown $modelBreak )
    {
        $this->model = $model;
        $this->modelDetail = $modelDetail;
        $this->modelBreak = $modelBreak;
    }

    public function all($request)
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
        $models = $this->model->filter($request)->
        orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
        ->where('document_id',$request->related_document_id)->where('complete_status','UnDelivered')
        ->where('branch_id',$request->branch_id);

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


    public function create($request){

        return DB::transaction(function () use ($request) {

            if (generalCheckDateModelFinancialYear($request['date']) == "true"){

                $data_request = $request;
                $serial = Serial::where([['branch_id',$request['branch_id']],['document_id',$request['document_id']]])->first();
                $data_request['serial_id'] = $serial ? $serial->id:null;
                $data = $this->model->create($data_request);
                if (isset($data_request['related_document_number'])){
                    $model_find =  $this->find($data_request['related_document_number']);
                    if ($model_find){
                        $model_find->update([
                            "complete_status" => 'Delivered'
                        ]);
                    }
                }
                foreach ($request['header_details'] as $header_details ){
                    $id =  $this->modelDetail->create(array_merge($header_details,['document_header_id'=>$data->id]));
                    foreach ($header_details['break_downs'] as $break){
                        $this->modelBreak->create(array_merge($break,['document_header_detail_id'=>$id->id]));
                    }
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
                        $id =  $this->modelDetail->create(array_merge($header_details,['document_header_id'=>$data->id]));
                        foreach ($header_details['break_downs'] as $break){
                            $this->modelBreak->create(array_merge($break,['document_header_detail_id'=>$id->id,'serial_number'=>$data->prefix]));
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
                foreach ($Details->itemBreakDowns as $break){
                    $break->delete();
                }
                $Details->delete();
            }
        }
        $model->delete();
    }
}
