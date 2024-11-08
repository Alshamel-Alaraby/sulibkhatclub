<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\EmployeePublicHolidayDetail;
use Modules\HR\Http\Requests\EmployeePublicHolidayDetailRequest;
use Modules\HR\Transformers\EmployeePublicHolidayDetailResource;

class EmployeePublicHolidayDetailController extends Controller
{
    public function __construct(private EmployeePublicHolidayDetail $model)
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

        return responseJson(200, 'success', EmployeePublicHolidayDetailResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new EmployeePublicHolidayDetailResource($model));
    }

    public function create(EmployeePublicHolidayDetailRequest $request)
    {

        $employee_public_holiday_header_id = $request->input('employee_public_holiday_header_id');
        $employeeIds = $request->input('employee_ids');

        if ($employeeIds) {
            foreach ($employeeIds as $employeeId) {
                $this->model->create([
                    'employee_public_holiday_header_id' => $employee_public_holiday_header_id,
                    'employee_id' => $employeeId,
                ]);
            }
        }

        return responseJson(200, 'success');

    }

    public function update(EmployeePublicHolidayDetailRequest $request)
    {

        $employee_public_holiday_header_id = $request->input('employee_public_holiday_header_id');
        $employeeIds = $request->input('employee_ids');

        if ($employee_public_holiday_header_id) {
            $this->model->where('employee_public_holiday_header_id', $employee_public_holiday_header_id)->delete();
        }

        if ($employeeIds) {
            foreach ($employeeIds as $employeeId) {
                $this->model->create([
                    'employee_public_holiday_header_id' => $employee_public_holiday_header_id,
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

}
