<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\AllRequest;
use Illuminate\Routing\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Requests\AllTaskPostRequest;
use Illuminate\Contracts\Support\Renderable;
use Modules\BoardsRent\Entities\EmployeeTask;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\BoardsRent\Http\Requests\TaskRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TaskController extends Controller
{
    public function __construct(private Task $model, private Media $media)
    {
    }

    public function find($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        return responseJson(200, 'success', new TaskResource($model));
    }

    public function all(AllRequest $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->employee_id) {
            $models->where('employee_id', $request->employee_id);
        }

        if ($request->customer_id) {
            $models->where('customer_id', $request->customer_id);
        }


        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TaskResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }


    public function allPost(AllTaskPostRequest $request)
    {
        $models = $this->model->where(function ($q) use ($request) {
            if ($request->customer_ids) {
                $q->whereIn('customer_id', $request->customer_ids);
            }
            if ($request->status_ids) {
                $q->whereIn('status_id', $request->status_ids);
            }
            if ($request->department_ids) {
                $q->whereIn('department_id', $request->department_ids);
            }
            if ($request->department_task_ids) {
                $q->whereIn('department_task_id', $request->department_task_ids);
            }
            if ($request->employee_ids) {
                $q->whereIn('employee_id', $request->employee_ids);
            }

            if ($request->year) {
                $q->whereYear('execution_date', $request->year);
            }

            if ($request->months_number) {
                $q->whereMonth('execution_date', $request->months_number);
            }

        })->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', TaskResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function create(TaskRequest $request)
    {
        $data = $request->validated();
        $model = $this->model->create($request->validated());
        if ($request->supervisors) {
            $model->supervisor()->attach($request->supervisors, [
                'type' => 'supervisor'
            ]);
        }

        if ($request->attentions) {
            $model->supervisor()->attach($request->attentions, [
                'type' => 'attention'
            ]);
        }

        $model->refresh();
        return responseJson(200, 'created', new TaskResource($model));
    }

    public function update($id, TaskRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->update($request->validated());

        if ($request->supervisors) {
            $model->supervisor()->delete();
            $model->supervisor()->attach($request->supervisors, [
                'type' => 'supervisor'
            ]);
        }

        if ($request->attentions) {
            $model->supervisor()->delete();
            $model->supervisor()->attach($request->attentions, [
                'type' => 'attention'
            ]);
        }

        if (request()->media && !request()->old_media) { // if there is new media and no old media
            $model->clearMediaCollection('media');
            foreach (request()->media as $media) {
                uploadImage($media, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if (request()->old_media && !request()->media) { // if there is old media and no new media
            $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                $media->delete();
            });
        }

        if (request()->old_media && request()->media) { // if there is old media and new media
            $model->media->whereNotIn('id', request()->old_media)->each(function (Media $media) {
                $media->delete();
            });
            foreach (request()->media as $image) {
                uploadImage($image, [
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        if (!request()->old_media && !request()->media) { // if this is no old media and new media
            $model->clearMediaCollection('media');
        }
        $model = $model->find($id);
        return responseJson(200, 'updated', new TaskResource($model));
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

    public function getRootNodes()
    {
        return $this->model->whereNull("parent_id")->get();
    }

    public function getChildNodes($parentId)
    {
        return $this->model->where("parent_id", $parentId)->get();
    }
}
