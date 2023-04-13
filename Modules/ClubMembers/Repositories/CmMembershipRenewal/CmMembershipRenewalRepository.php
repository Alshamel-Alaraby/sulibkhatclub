<?php

namespace Modules\ClubMembers\Repositories\CmMembershipRenewal;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMembershipRenewal;

class CmMembershipRenewalRepository implements CmMembershipRenewalInterface
{
    public function __construct(private CmMembershipRenewal $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->where(function ($q) use ($request) {
            if ($request->from && !$request->to) {
                $from = $request->from;
                $from = explode('-', $from);
                $from = $from[1] . '-' . $from[2];
                $q->where('from', $from);
            }
            if ($request->to && !$request->from) {
                $to = $request->to;
                $to = explode('-', $to);
                $to = $to[1] . '-' . $to[2];
                $q->where('to', $to);
            }

            if ($request->from && $request->to) {
                $from = $request->from;
                $from = explode('-', $from);
                $from = $from[1] . '-' . $from[2];

                $to = $request->to;
                $to = explode('-', $to);
                $to = $to[1] . '-' . $to[2];

                $q->where('from', $from);
                $q->where('to', $to);
            }
        })->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

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

    public function create($request)
    {
        //return $request->memberships_renewals;

        //foreach ($request->memberships_renewals as $membership_renewal){
        //    return $membership_renewal;
        //}

        // creation in database happens here !!
        DB::transaction(function () use ($request) {
            foreach ($request['memberships_renewals'] as $membership_renewal) {
                $this->model->create($membership_renewal);
            }

        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $this->model->where("id", $id)->update($request);
        });

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

}
