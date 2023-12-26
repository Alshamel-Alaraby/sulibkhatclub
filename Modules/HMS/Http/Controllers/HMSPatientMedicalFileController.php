<?php

namespace Modules\HMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSPatientMedicalFile;
use Modules\HMS\Http\Requests\HMSPatientMedicalFileRequest;
use Modules\HMS\Transformers\HMSPatientMedicalFileResource;

class HMSPatientMedicalFileController extends Controller
{
    public function __construct(private HMSPatientMedicalFile $model)
    {
        $this->model = $model;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSPatientMedicalFileRequest $request)
    {
        $model = $this->model->create($request->except(['media']));
        createFile($request->media,$model);
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSPatientMedicalFile $patient_medical_file)
    {
        createFile([],$patient_medical_file);
        $patient_medical_file->delete();
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
