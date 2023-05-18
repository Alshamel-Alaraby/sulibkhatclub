<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\JobTitle;
use Modules\HR\Http\Requests\JobTitleRequest;
use Modules\HR\Transformers\JobTitleResource;

class JobTitleController extends Controller
{
    public function __construct(private JobTitle $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', JobTitleResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(JobTitleRequest $request)
    {
        $model = $this->model->create($request->validated());
        return responseJson(200, 'success', new JobTitleResource($model));

    }

    public function update($id, JobTitleRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        return responseJson(200, 'updated', new JobTitleResource($model));
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->delete();
        return responseJson(200, 'deleted');
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();
        if ($models->count() != count($ids)) {
            return responseJson(404, 'not found');
        }
        $models->each(function ($model) {
            $model->delete();
        });
        return responseJson(200, 'deleted');
    }

    public function logs($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $logs = $model->activities()->orderBy('created_at', 'DESC')->get();
        return responseJson(200, 'success', \App\Http\Resources\Log\LogResource::collection($logs));
    }

    public function processJsonData(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        foreach ($data['data'] as $item) {
            switch ($item['op']) {
                case 'ADD':
                    $this->model->insert([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    break;
                case 'UPD':
                    $this->model->where('id', $item['id'])->update([
                        'name' => $item['name'],
                        'name_e' => $item['name_e'],
                        'updated_at' => now(),
                    ]);
                    break;
                case 'DEL':
                    $this->model->where('id', $item['id'])->delete();
                    break;
            }
        }

        return responseJson(200, 'Done');
    }
}
