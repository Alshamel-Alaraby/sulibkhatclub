<?php

namespace App\Repositories\Employee;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeRepository implements EmployeeInterface
{

    public function __construct(private Employee $model)
    {
        $this->model = $model;

    }

    public function all($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->customer_handel) {
            $models->where('customer_handel', '!=', 'non_customer');
        }
        if ($request->is_salesman) {
            $models->where('is_salesman', 'true');
        }

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
        DB::transaction(function () use ($request) {
            $model = $this->model->create($request->all());
            if ($request->plans) {
                $model->plans()->sync($request->plans);
            }

            return $model;
        });
    }

    public function update($request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $model = $this->model->find($id);
            $model->update($request->except(["media"]));
            if ($request->plans) {
                $model->plans()->sync($request->plans);
            }
        });

    }

    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
    }

    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }

    public function processJsonData(array $data): void
    {
        foreach ($data['data'] as $item) {
            switch ($item['op']) {
                case 'ADD':
                    $this->model->insert([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'department_id' => $item['department_id'] ?? null,
                        'job_id' => $item['job_id'] ?? null,
                        'manager_id' => $item['manager_id'] ?? null,
                        'branch_id' => $item['branch_id'] ?? null,
                        'manage_others' => $item['manage_others'] ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;
                case 'UPD':
                    $this->model->where('id', $item['id'])->update([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'department_id' => $item['department_id'] ?? null,
                        'job_id' => $item['job_id'] ?? null,
                        'manager_id' => $item['manager_id'] ?? null,
                        'branch_id' => $item['branch_id'] ?? null,
                        'manage_others' => $item['manage_others'] ?? 1,
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
