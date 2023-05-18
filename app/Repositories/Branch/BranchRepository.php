<?php

namespace App\Repositories\Branch;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class BranchRepository implements BranchRepositoryInterface
{
    public $model;
    public function __construct(Branch $model)
    {
        $this->model = $model;
    }
    public function getAllBranches($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->document_id) {
            $models->whereHas('serials', function ($q) use ($request) {
                $q->where('document_id', $request->document_id);
            });
        }

        if ($request->notParent) {
            $models->whereNotNull('parent_id');
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create($data)
    {
        DB::transaction(function () use ($data) {
            $this->model->create($data);
            // cacheForget("branches");
        });
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($data, $id)
    {
        DB::transaction(function () use ($id, $data) {
            $this->model->where("id", $id)->update($data);
            // $this->forget($id);
        });
    }
    public function logs($id)
    {
        return $this->model->find($id)->activities()->orderBy('created_at', 'DESC')->get();
    }
    public function delete($id)
    {
        $model = $this->find($id);
        if ($model) {
            // $this->forget($id);
            $model->delete();
        }
    }

    // private function forget($id)
    // {
    //     $keys = [
    //         "branches",
    //         "branches_" . $id,
    //     ];
    //     foreach ($keys as $key) {
    //         cacheForget($key);
    //     }

    // }

    public function processJsonData(array $data): void
    {
        foreach ($data['data'] as $item) {
            switch ($item['op']) {
                case 'ADD':
                   $this->model->insert([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'company_id' => $item['company_id'] ?? null,
                        'parent_id' => $item['parent_id'] ?? null,
                        'is_active' => $item['is_active'] ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;
                case 'UPD':
                   $this->model->where('id', $item['id'])->update([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'company_id' => $item['company_id'] ?? null,
                        'parent_id' => $item['parent_id'] ?? null,
                        'is_active' => $item['is_active'] ?? 1,
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
