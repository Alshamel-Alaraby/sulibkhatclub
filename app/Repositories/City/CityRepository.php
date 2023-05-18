<?php

namespace App\Repositories\City;

use App\Models\City;
use Illuminate\Support\Facades\DB;

class CityRepository implements CityRepositoryInterface
{
    public $model;
    public function __construct(City $model)
    {
        $this->model = $model;
    }
    public function getAll($request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');
        if ($request->country_id){
            $models->where('country_id',$request->country_id);
        }

        if ($request->governorate_id){
            $models->where('governorate_id',$request->governorate_id);
        }

        if ($request->per_page) {
            return ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            return ['data' => $models->get(), 'paginate' => false];
        }
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            cacheForget("cities");
            return $this->model->create($data);
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
            $this->forget($id);
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
            $this->forget($id);
            $model->delete();
        }
    }

    private function forget($id)
    {
        $keys = [
            "cities",
            "cities_" . $id,
        ];
        foreach ($keys as $key) {
            cacheForget($key);
        }

    }
}
