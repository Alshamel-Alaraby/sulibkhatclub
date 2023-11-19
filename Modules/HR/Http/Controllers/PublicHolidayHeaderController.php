<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Entities\PublicHolidayDetail;
use Modules\HR\Entities\PublicHolidayHeader;
use Modules\HR\Http\Requests\PublicHolidayHeaderRequest;
use Modules\HR\Transformers\PublicHolidayHeaderResource;

class PublicHolidayHeaderController extends Controller
{
    public function __construct(private PublicHolidayHeader $model, private PublicHolidayDetail $publicHolidayDetailModel)
    {
        $this->model = $model;
        $this->publicHolidayDetailModel = $publicHolidayDetailModel;
    }

    public function all(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', PublicHolidayHeaderResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function find($id)
    {
        $model = $this->model->data()->find($id);
        if (!$model) {
            return responseJson(404, __('message.data not found'));
        }
        return responseJson(200, 'success', new PublicHolidayHeaderResource($model));
    }

    public function create(PublicHolidayHeaderRequest $request)
    {

        $model = $this->model->create($request->validated());

        if ($model) {
            if ($request['public_holiday_details']) {
                foreach ($request['public_holiday_details'] as $public_holiday_detail) {
                    $this->publicHolidayDetailModel->create([
                        'public_holiday_header_id' => $model['id'],
                        'vdate' => $public_holiday_detail['vdate'],
                        'name' => $public_holiday_detail['name'],
                        'name_e' => $public_holiday_detail['name_e'],
                    ]);

                }
            }
        }
        return responseJson(200, 'success', new PublicHolidayHeaderResource($model));

    }

    public function update($id, PublicHolidayHeaderRequest $request)
    {
        $model = $this->model->find($id);

        if ($model) {
            $this->publicHolidayDetailModel->where('public_holiday_header_id', $model->id)->delete();
            $this->model->where('id', $model->id)->delete();
        }

        $model = $this->model->create($request->validated());

        if ($model) {
            if ($request['public_holiday_details']) {
                foreach ($request['public_holiday_details'] as $public_holiday_detail) {
                    $this->publicHolidayDetailModel->create([
                        'public_holiday_header_id' => $model['id'],
                        'vdate' => $public_holiday_detail['vdate'],
                        'name' => $public_holiday_detail['name'],
                        'name_e' => $public_holiday_detail['name_e'],
                    ]);

                }
            }
        }

        return responseJson(200, 'updated');
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!$model) {
            return responseJson(404, 'not found');
        }

        if ($model) {
            $this->publicHolidayDetailModel->where('public_holiday_header_id', $model->id)->delete();
            $this->model->where('id', $model->id)->delete();
        }
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
            $this->publicHolidayDetailModel->where('public_holiday_header_id', $model->id)->delete();

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
