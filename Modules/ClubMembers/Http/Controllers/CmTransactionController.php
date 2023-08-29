<?php

namespace Modules\ClubMembers\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Http\Requests\CmTransactionRequest;
use Modules\ClubMembers\Repositories\CmMemberSetting\CmMemberSettingInterface;
use Modules\ClubMembers\Repositories\CmTransaction\CmTransactionInterface;
use Modules\ClubMembers\Transformers\CheckDateMemberTransactionResource;
use Modules\ClubMembers\Transformers\CmMemberResource;
use Modules\ClubMembers\Transformers\CmTransactionResource;

use Modules\ClubMembers\Entities\CmTransaction;
use Modules\ClubMembers\Transformers\CmMemberTransactionsResource;

class CmTransactionController extends Controller
{
    public function __construct(private CmTransactionInterface $modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function all(Request $request)
    {
        $models = $this->modelInterface->all($request);

        return responseJson(200, 'success', CmTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {

        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new CmTransactionResource($model));
    }

    public function create(CmTransactionRequest $request)
    {
        $model = $this->modelInterface->create($request);
        return responseJson(200, 'success');

    }

    public function update(CmTransactionRequest $request, $id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $model = $this->modelInterface->update($request, $id);

        return responseJson(200, 'success', new CmTransactionResource($model));
    }


    public function delete($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        if ($model->haveChildren) {
            return responseJson(400, __('message.parent have children'));
        }
        $this->modelInterface->delete($id);

        return responseJson(200, 'success');
    }

    public function logs($id)
    {
        $model = $this->modelInterface->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        $logs = $this->modelInterface->logs($id);
        return responseJson(200, 'success', $logs);
    }

    public function bulkDelete(Request $request)
    {
        foreach ($request->ids as $id) {
            $model = $this->modelInterface->find($id);
            $arr = [];
            if ($model->have_children) {
                $arr[] = $id;
                continue;
            }
            $this->modelInterface->delete($id);
        }
        if (count($arr) > 0) {
            return responseJson(400, __('some items has relation cant delete'));
        }
        return responseJson(200, __('Done'));
    }


    public function MemberTransactions($member_id)
    {

        $models = CmTransaction::where("cm_member_id", $member_id)->get();

        return responseJson(200, 'success', CmMemberTransactionsResource::collection($models));

    }

    public function checkDateMemberTransaction(Request $request)
    {
        $models['data'] = CmTransaction::whereDate('date','<=' ,$request->date)->where('year_to',$request->year)->paginate($request->per_page);
        $models['paginate'] = true;
        return responseJson(200, 'success', CheckDateMemberTransactionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }
    public function unpaidMemberTransaction(Request $request)
    {
       $transactionArray = CmTransaction::whereDate('date','<=' ,$request->date)->where('year_to',$request->year)->pluck('cm_member_id')->toArray();
        $models['data'] = CmMember::whereNotIn('id',$transactionArray)->paginate($request->per_page);
        $models['paginate'] = true;

        return responseJson(200, 'success', CmMemberResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);

    }

    // public function financialStatusCheck()
    // {

    //     $paidMembers = CmTransaction::where('date', '>', '2022-12-31')->value('cm_member_id')->get();

    //     foreach($paidMembers as $member_id)
    //     {

    //         CmMember::where('id', $member_id)->value('')



    //     }








    // }


}
