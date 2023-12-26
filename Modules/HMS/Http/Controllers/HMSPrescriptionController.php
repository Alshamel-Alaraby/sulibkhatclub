<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSPrescription;
use Modules\HMS\Http\Requests\HMSPrescriptionRequest;
use Modules\HMS\Transformers\HMSPrescriptionResource;

class HMSPrescriptionController extends Controller
{
    public function __construct(private HMSPrescription $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSPrescriptionResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSPrescriptionRequest $request)
    {

        $prescription = $this->model->create($request->only(['doctor_id','patient_id','appointment_id']));
        $this->prescription_drug_list($request->drug_list,$prescription);
        $this->prescription_diagnosis_tests($request->diagnosis_test_list,$prescription);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSPrescriptionRequest $request, HMSPrescription $prescription)
    {
        $prescription->drug_list()->forceDelete();
        $prescription->diagnosis_list()->forceDelete();
        $this->prescription_drug_list($request->drug_list,$prescription);
        $this->prescription_diagnosis_tests($request->diagnosis_test_list,$prescription);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSPrescription $prescription)
    {
        $prescription->delete();
    }

    protected function prescription_drug_list($drug_list,$prescription){
        foreach($drug_list as $list){
            if(collect($list)->filter()->count() > 0)
            $prescription->drug_list()->create($list);
        }
    }
    protected function prescription_diagnosis_tests($diagnosis_test_list,$prescription){
        foreach($diagnosis_test_list as $list){
            if(collect($list)->filter()->count() > 0)
                $prescription->diagnosis_list()->create($list);
        }

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
