<?php

namespace App\Repositories\Translation;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TranslationRepository implements TranslationInterface
{

    public function __construct(private Transaction $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->invoice_id) {
            $models->where('invoice_id', $request->invoice_id);
        }
        if ($request->module_type == "club")
        {
            $models->where('module_type', "club");
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
        DB::transaction(function () use ($request) {
            foreach ($request['transactions'] as $transaction):
                $this->model->create($transaction);
            endforeach;
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
