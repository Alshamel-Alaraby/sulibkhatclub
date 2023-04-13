<?php

namespace Modules\ClubMembers\Repositories\CmMember;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;

class CmMemberRepository implements CmMemberInterface
{

    public function __construct(private CmMember $model)
    {
        $this->model = $model;
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
            return $this->model->create($request->all());
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request->all());
        });

        $model = $this->model->find($id);
        return $model;
    }

//     membership_number
// applying_number

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
/*
public function updateSponsor($request, $sponsor_id)
{
DB::transaction(function () use ($sponsor_id, $request) {
$this->model->where("sponsor_id", $sponsor_id)->update($request->all());
});

$models = $this->model->where("sponsor_id", $sponsor_id)->get();
return $models;
}
 */

    public function updateSponsor($request, $sponsor_id)
    {
        DB::transaction(function () use ($sponsor_id, $request) {
            $this->model->where("sponsor_id", $sponsor_id)->update($request->all());
        });

        $updatedModels = $this->model->where("sponsor_id", $request->sponsor_id)->get();
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

}
