<?php

namespace Modules\HMS\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSPatient;
use Modules\HMS\Http\Requests\HMSPatientRequest;
use Modules\HMS\Transformers\HMSPatientAllDataResource;
use Modules\HMS\Transformers\HMSPatientResource;

class HMSPatientController extends Controller
{
    public function __construct(private HMSPatient $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        if($request->drop_down)
            return $this->model->select('id','name','name_e')->latest()->get();

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {

            $models = $models->paginate($request->per_page);

            //return the selected module from dropdown when append module to tree menu
            if($request->selected_patient_id != 0){
                $selected_patinet_in_dropdown =$this->model->where('id',$request->selected_patient_id)->get()->merge($models->items());
                $models->setCollection($selected_patinet_in_dropdown);
            }

            $models = ['data' => $models, 'paginate' => true];

        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSPatientResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    public function get_patient_data(HMSPatient $patient)
    {
        return responseJson(200, 'success',new HMSPatientAllDataResource($patient),null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSPatientRequest $request)
    {
        if($request->type == 'inpatient')
            $user = $this->create_user($request);
        $this->model->create($this->patient_data($request,$request->type =='inpatient' ? $user->id : null));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSPatientRequest $request, HMSPatient $patient)
    {
        $patient_data = $request->only(['name','name_e','mobile','whatsapp','email','type','gender','address','blood','date_of_birth','safe_number','code_country','patient_weight','patient_height']);
        $user_data = $request->only(['name','name_e','email','company_id','is_active','password']);
        $patient->update($patient_data);

        if($request->type == 'inpatient'){
            if($patient->user)
                $patient->user->update($user_data);
            else{
                $user = $patient->user()->create($user_data);
                $patient->update(['user_id' => $user->id]);
            }

        }


    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSPatient $patient)
    {
        $patient->user->delete();
        $patient->delete();
    }

    protected function patient_data($request,$user_id){
        $data = $request->only(['name','name_e','mobile','type','whatsapp','email','blood','gender','date_of_birth','address','safe_number','code_country','patient_weight','patient_height']);
        $data['user_id'] = $user_id;
        return $data;
    }



    protected function create_user($request){
        $data = $request->only(['name','name_e','email','company_id','is_active','password']);
        $data['type'] = 'patient';
        return User::create($data);
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
