<?php

namespace App\Repositories\Depertment;

use Illuminate\Support\Facades\DB;

class DepertmentRepository implements DepertmentInterface
{

    public function __construct(private \App\Models\Depertment $model)
    {
        $this->model = $model;
    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->employees) {
            $models->whereHas('employees');
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
            $data = $this->model->create($request);
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

    public function processJsonData(array $data): void
    {
        foreach ($data['data'] as $item) {
            switch ($item['op']) {
                case 'ADD':
                    $this->model->insert([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'supervisor_id' => $item['supervisor_id'] ?? null,
                        'attention_id' => $item['attention_id'] ?? null,
                        'company_id' => $item['company_id'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;
                case 'UPD':
                    $this->model->where('id', $item['id'])->update([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'supervisor_id' => $item['supervisor_id'] ?? null,
                        'attention_id' => $item['attention_id'] ?? null,
                        'company_id' => $item['company_id'] ?? null,
                        'updated_at' => now(),
                    ]);
                    break;
                case 'DEL':
                    $this->model->where('id', $item['id'])->delete();
                    break;
            }
        }
    }
}
