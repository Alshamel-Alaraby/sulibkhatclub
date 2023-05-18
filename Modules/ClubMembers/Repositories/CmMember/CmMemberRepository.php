<?php

namespace Modules\ClubMembers\Repositories\CmMember;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmHistoryTransform;
use Modules\ClubMembers\Entities\CmMember;

class CmMemberRepository implements CmMemberInterface
{

    public function __construct(private CmMember $model, private CmHistoryTransform $modelCmHistoryTransform)
    {
        $this->model = $model;
        $this->modelCmHistoryTransform = $modelCmHistoryTransform;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->financial_status_id) {
            $models->where('financial_status_id', $request->financial_status_id);
        }
        if ($request->national_id) {
            $models->where('national_id', $request->national_id);
        }

        if ($request->member_id){
            $models->where('id', $request->member_id);
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
            return $this->model->create(array_merge($request,['full_name' => $full_name]));
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
                ]));
        });

        $model = $this->model->find($id);
        return $model;
    }

    public function updateDecline($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update(array_merge($request->all(), ['acceptance' => 2]));
        });

        $model = $this->model->find($id);
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
        DB::transaction(function () use ($request) {

            foreach ($request['accept-members'] as $accept_member) {
                $max_membership_number = $this->model->max('membership_number');
                $max = $max_membership_number + 1;

                $member = $this->model->where('id', $accept_member['id'])->first();
                if ($member) {

                    $member->update(array_merge($accept_member,
                        [
                            'acceptance' => 1,
                            'membership_number' => $max,
                        ]));
                }
            }
        });
    }

}
