<?php

namespace App\Repositories\VoucherHeader;

use App\Models\Serial;
use Illuminate\Support\Facades\DB;

class VoucherHeaderRepository implements VoucherHeaderInterface
{
    public function __construct(private \App\Models\VoucherHeader $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if($request->document_id)
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

    public function create($request)
    {
        return DB::transaction(function () use ($request) {
            $data_request = $request;
            $serial = Serial::where([['branch_id',$request['branch_id']],['document_id',$request['document_id']]])->first();
            $data_request['serial_id'] = $serial ? $serial->id:null;
            $data = $this->model->create($data_request);
            return $data;
        });
    }

    public function update($request, $id)
    {
        $data = $this->model->find($id);
        $data->update($request);
        return $data;
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
