<?php

namespace Modules\ClubMembers\Repositories\CmMember;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmHistoryTransform;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmMemberPermission;
use Modules\ClubMembers\Entities\CmMemberRequest;
use Modules\ClubMembers\Entities\CmTransaction;
use Modules\ClubMembers\Entities\CmTypePermission;

class CmMemberRepository implements CmMemberInterface
{

    public function __construct(private CmMember $model, private CmHistoryTransform $modelCmHistoryTransform, CmMemberRequest $modelRequest ,CmTransaction $modelTransaction )
    {
        $this->model = $model;
        $this->modelRequest = $modelRequest;
        $this->modelCmHistoryTransform = $modelCmHistoryTransform;
        $this->modelTransaction = $modelTransaction;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->financial_status_id) {
            $models->where('financial_status_id', $request->financial_status_id);
        }
        if ($request->member_type_id) {
            $models->where('member_type_id', $request->member_type_id);
        }

        if ($request->member_id){
            $models->where('id', $request->member_id);
        }
        if ($request->hasTransaction){
            $models->whereHas('cmTransaction');
        }
        if ($request->sponsor_id) {
            $models->where('sponsor_id', $request->sponsor_id);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } elseif ($request->limet){
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        }else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }



    public function find($id)
    {
        return $this->model->find($id);
    }

    public function allAcceptancePending($request)
    {

        $models = $this->model->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->where('acceptance', '0');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function create($request)
    {

        return DB::transaction(function () use ($request) {
            $full_name = ($request['first_name'] ?? '') . ' ' .
                ($request['second_name'] ?? '') . ' ' .
                ($request['third_name'] ?? '') . ' ' .
                ($request['last_name'] ?? '') . ' ' .
                ($request['family_name'] ?? '') ;
            return $this->model->create(array_merge($request,['full_name' => $full_name,'member_type_id'=>4]));
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $full_name = ($request['first_name'] ?? '') . ' ' .
                ($request['second_name'] ?? '') . ' ' .
                ($request['third_name'] ?? '') . ' ' .
                ($request['last_name'] ?? '') . ' ' .
                ($request['family_name'] ?? '') ;
            $this->model->where("id", $id)->update(array_merge($request,['full_name' => $full_name]));
        });
        $model = $this->model->find($id);
        return $model;
        //
    }

    public function updateAcceptance($request, $id)
    {

        DB::transaction(function () use ($id, $request) {
            $increment_member_number = $this->model->max('membership_number');
            $new_member_number = $increment_member_number + 1;

            $increment_applying_number = $this->model->max('applying_number');
            $new_applying_number = $increment_applying_number + 1;

            $this->model->where("id", $id)->update(array_merge($request->all(),
                [
                    'acceptance' => 1,
                    'membership_number' => $new_member_number,
                    'applying_number' => $new_applying_number,
                    'financial_status_id' => 3,
                    'member_type_id' => 4,
                    'status_id' => 2,
                ]));
        });

        $model = $this->model->find($id);
        return $model;
    }

    public function updateDecline($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->modelRequest->where("id", $id)->update(array_merge($request->all(), ['acceptance' => 2,'member_type_id'=>16]));
        });
        $model = $this->modelRequest->find($id);
        return $model;
    }

    public function updateSponsor($request, $sponsor_id)
    {
        // return now()->format('d-m-Y H:i:s') ;
        DB::transaction(function () use ($sponsor_id, $request) {
            $models = $this->model->where("sponsor_id", $sponsor_id)->get();
            foreach ($models as $model) {
                $this->modelCmHistoryTransform->create([
                    'sponser_id_from' => $model->sponsor_id,
                    'sponser_id_to' => $request->sponsor_id,
                    'member_id' => $model->id,
                    'date' => now(),
                ]);
                $model->update($request->all());
            }
        });

        $updatedModels = $this->model->where("sponsor_id", $request->sponsor_id)->where('updated_at', now()->format('y-m-d H:i:s'))->get();
        return $updatedModels;
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function allAcceptance($request)
    {

        $models = $this->model->filter($request)
            ->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC')
            ->where('acceptance', '1');

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function acceptMembers($request)
    {
       return DB::transaction(function () use ($request) {

            foreach ($request['accept-members'] as $accept_member) {
                $max_membership_number = $this->model->max('membership_number');
                $max = $max_membership_number + 1;

                $memberRequest  = $this->modelRequest->where('id', $accept_member['id'])->first();
                if ($memberRequest){
                     $membercreate = collect($memberRequest)->except(['id','deleted_at','created_at','updated_at']) ;
                     $model = $this->model->create($membercreate->all());
                     $accept =  collect($accept_member)->except(['id']);
                    $model->update(array_merge($accept->all(),
                        [
                            'acceptance' => 1,
                            'membership_number' => $max,
                        ]));
                    $transaction = $this->modelTransaction->where('member_request_id',$memberRequest->id)->first();
                    if ($transaction){
                        $transaction->update([
                            'cm_member_id' => $model->id,
                            'member_request_id' => null,

                        ]);
                    }
                    $memberRequest->delete();
                }
            }
           return 200;

       });
    }

    public function reportCmMember($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->cm_permissions_id == 1) {
            $models->whereIn('auto_member_type_id', [1,2,3]);
        }
        if ($request->cm_permissions_id == 2) {
            $models->where('auto_member_type_id', 2);
        }

        if ($request->cm_permissions_id == 3){
            $models->whereIn('auto_member_type_id', [2,3]);
        }

        if ($request->cm_permissions_id == "0"){
            $models->where('auto_member_type_id', null);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }

    }

    public function updateCmMember()
    {
        $All_Members_FatherIdOne = $this->model->whereIn('member_type_id', [4,5,11,12,17,19])->get();

        ///Last_Member_transaction insert in cm_member
        /*foreach ($All_Members_FatherIdOne as $Member){
              $Last_Member_transaction  = DB::table('cm_transactions')->where('cm_member_id',$Member->id)->orderBy('date', 'DESC')->first();
              $this->model->where('id',$Member->id)->update(['last_transaction_date' => $Last_Member_transaction->date]);
        }*/

        /////type_permissions
        $permission_one     =   DB::table('cm_type_permissions')->where('id',4)->first();
        $permission_two     =   DB::table('cm_type_permissions')->where('id',5)->first();
        $permission_three   =   DB::table('cm_type_permissions')->where('id',6)->first();

        foreach ($All_Members_FatherIdOne as $Member){
            ///First Condition
            $dbDate                    = \Carbon\Carbon::parse($Member->membership_date);
            $diffYears                 = \Carbon\Carbon::now()->diffInYears($dbDate);

            ///Second Condition
            $Last_Member_transaction  = $Member->last_transaction_date;
            $Last_date                = \Carbon\Carbon::parse($Last_Member_transaction)->format('d');


            $permission_one_Days      = \Carbon\Carbon::parse("2023-" . $permission_two->allowed_subscription_date)->format('d');
            $permission_two_Days      = \Carbon\Carbon::parse("2023-" .$permission_two->allowed_subscription_date)->format('d');
            $permission_three_Days      = \Carbon\Carbon::parse("2023-" .$permission_three->allowed_subscription_date)->format('d');


            if($permission_one->membership_period >= $diffYears && $Last_date >= $permission_one_Days){
                $this->model->where('id',$Member->id)->update(['auto_member_type_id' => 1]);
            }
            if($permission_one->membership_period >= $diffYears && $Last_date >= $permission_two_Days){
                $this->model->where('id',$Member->id)->update(['auto_member_type_id' => 2]);
            }

            if($permission_one->membership_period >= $diffYears && $Last_date >= $permission_three_Days){
                $this->model->where('id',$Member->id)->update(['auto_member_type_id' => 3]);
            }

        }
        return 200;
    }

    public function getSponsors($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->sponsor_id) {
            $models->where('sponsor_id', $request->sponsor_id);
        }

        if($request->memberNumber){
            $memberIds = $this->model->where('sponsor_id', $request->sponsor_id)->pluck('id');
        }
        // return $memberIds;

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true , 'memberIds' => $memberIds];
        } elseif ($request->limet){
            return ['data' => $models->take($request->limet)->get(), 'paginate' => false];
        }else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }


}
