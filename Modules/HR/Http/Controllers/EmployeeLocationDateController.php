<?php

namespace Modules\HR\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeeLocationDate;
use Modules\HR\Http\Requests\EmployeeLocationDateRequest;
use Modules\HR\Transformers\EmployeeLocationDateResource;

class EmployeeLocationDateController extends Controller
{
    public function __construct(private EmployeeLocationDate $model, private Employee $employeeModel)
    {
        $this->model = $model;
        $this->employeeModel = $employeeModel;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', EmployeeLocationDateResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new EmployeeLocationDateResource($model));
    }

    public function create(EmployeeLocationDateRequest $request)
    {

        $locationDateId = $request->input('location_date_id');
        $employeeIds = $request->input('employee_ids');

        if ($employeeIds) {
            foreach ($employeeIds as $employeeId) {
                $this->model->create([
                    'location_date_id' => $locationDateId,
                    'employee_id' => $employeeId,
                ]);
            }
        }

        return responseJson(200, 'success');

    }

    public function update(EmployeeLocationDateRequest $request)
    {

        $locationDateId = $request->input('location_date_id');
        $employeeIds = $request->input('employee_ids');

        if ($locationDateId) {
            $this->model->where('location_date_id', $locationDateId)->delete();
        }

        if ($employeeIds) {
            foreach ($employeeIds as $employeeId) {
                $this->model->create([
                    'location_date_id' => $locationDateId,
                    'employee_id' => $employeeId,
                ]);
            }
        }

        // $model->refresh();
        return responseJson(200, 'updated');
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

    public function employees()
    {
        $models = $this->model->pluck('employee_id');
        $employees = $this->employeeModel->whereNotIn('id', $models)->get();
        return responseJson(200, 'success', $employees);
    }
}
