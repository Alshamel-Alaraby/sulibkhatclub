<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeePublicHolidayHeader;
use Modules\HR\Http\Requests\EmployeePublicHolidayHeaderRequest;
use Modules\HR\Transformers\EmployeePublicHolidayHeaderResource;

class EmployeePublicHolidayHeaderController extends Controller
{
    public function __construct(private EmployeePublicHolidayHeader $model)
    {
        $this->model = $model;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->public_holiday_header_id) {
            $models->where('public_holiday_header_id', $request->public_holiday_header_id);
        }
        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', EmployeePublicHolidayHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new EmployeePublicHolidayHeaderResource($model));
    }

    public function create(EmployeePublicHolidayHeaderRequest $request)
    {
        $model = $this->model->create($request->validated());
        $model->refresh();
        // $model->employees()->attach($request->employees);
        return responseJson(200, 'success');

    }

    public function update($id, EmployeePublicHolidayHeaderRequest $request)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        $model->update($request->validated());
        $model->refresh();
        // $model->employees()->sync($request->employees);
        return responseJson(200, 'updated');
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }
        $model->employees()->detach();

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
            // $model->employees()->detach();

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
}
