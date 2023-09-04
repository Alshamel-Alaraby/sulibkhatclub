<?php

namespace Modules\ClubMembers\Repositories\CmTransaction;

use App\Models\Serial;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Entities\CmSponser;
use Modules\ClubMembers\Entities\CmTransaction;
use Modules\RealEstate\Entities\RlstCategoriesItem;

class CmTransactionRepository implements CmTransactionInterface
{

    public function __construct(private CmTransaction $model , private CmMember $modelMember ,private CmMemberRequest $modelMemberRequest)
    {
        $this->model       = $model;
        $this->modelMember = $modelMember;
        $this->modelMemberRequest = $modelMemberRequest;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->invoice_id) {
            $models->where('invoice_id', $request->invoice_id);
        }
        if ($request->sponsor == 0)
        {
            $models->whereNull('sponsor_id');
        }
        if ($request->sponsor == 1)
        {
            $models->whereNotNull('sponsor_id');
        }

        if ($request->document_id)
        {
            $models->where('document_id',$request->document_id);
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

    public function findCmMemberLastTransaction($id)
    {
        $data = $this->model->where('cm_member_id',$id)->latest()->first();
        return $data;
    }

    public function create($request)
    {
        DB::transaction(function () use ($request) {
            foreach ($request['transactions'] as $transaction):
                if ($transaction['module_type'] == 'club' && !$transaction['serial_id'])
                {
                    $serial = Serial::where([['branch_id',$transaction['branch_id']],['document_id',$transaction['document_id']]])->first();
                    $transaction['serial_id'] = $serial ? $serial->id:null;
                }
                $model= $this->model->create($transaction);
                if (isset($transaction['cm_member_id']))
                {
                    $member =  $this->modelMember->find($transaction['cm_member_id']);
                    if ($member){
                        $member->update(['last_transaction_date'=>$transaction['date']]);
                    }
                }
                if ( isset($transaction['member_request_id']) ){
                    $memberRequest =  $this->modelMemberRequest->find($transaction['member_request_id']);
                    if ($memberRequest){
                        $memberRequest->update(['last_transaction_date'=>$transaction['date']]);
                    }
                }

                if ($transaction['serial_id'])
                {
                    $serials = generalSerialWithIdCreate($model,$transaction['serial_id']);
                }else{
                    $serials = generalSerial($model);
                }
                $model->update([
                    "serial_number" => $serials['serial_number'],
                    "prefix" => $serials['prefix'],
                ]);
            endforeach;
        });
    }

    public function update($request, $id)
    {
        $model = $this->model->find($id);
        $model->update($request);
        $transaction = $this->model->where('cm_member_id',$model->cm_member_id)->orderBy('id', 'desc')->first();

        if ($transaction->id  == $model->id){
            $member =  $this->modelMember->find($model->cm_member_id);
            $member->update(['last_transaction_date'=>$request['date']]);

        }

        return $model;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        $model->delete();
    }

}
