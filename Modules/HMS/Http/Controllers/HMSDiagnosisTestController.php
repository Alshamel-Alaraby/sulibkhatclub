<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSDiagnosisTest;
use Modules\HMS\Http\Requests\HMSDiagnosisTestRequest;
use Modules\HMS\Transformers\HMSDiagnosisTestResource;

class HMSDiagnosisTestController extends Controller
{
    public function __construct(private HMSDiagnosisTest $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        if($request->drop_down)
            return $this->model->select('id','name','name_e')->latest()->get();

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSDiagnosisTestResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSDiagnosisTestRequest $request)
    {
        $this->model->create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSDiagnosisTestRequest $request, HMSDiagnosisTest $diagnosis_test)
    {
        $diagnosis_test->update($request->validated());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSDiagnosisTest $diagnosis_test)
    {
        $diagnosis_test->delete();
    }

    public function bulkDelete()
    {

        $ids = request()->ids;
        if (!$ids) {
            return responseJson(400, 'ids is required');
        }
        $models = $this->model->whereIn('id', $ids)->get();

        $models->each(function ($model) {
//            if (!$model->hasChildren()) {
            $model->delete();
//            }

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
