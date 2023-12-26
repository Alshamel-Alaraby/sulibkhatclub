<?php

namespace Modules\HMS\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HMS\Entities\HMSDoctor;
use Modules\HMS\Entities\HMSDoctorsWorkTime;
use Modules\HMS\Http\Requests\HMSDoctorRequest;
use Modules\HMS\Transformers\HMSDoctorResource;

class HMSDoctorController extends Controller
{
    public function __construct(private HMSDoctor $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        if($request->drop_down){
            return $this->model->select('id','name','name_e','employee_id')
            ->where(function($q) use($request){
                if($request->branch_id)
                $q->whereRelation('work_times.room','branch_id',$request->branch_id);
                if($request->room_id)
                    $q->whereRelation('work_times','room_id',$request->room_id);
            })
            ->when($request->outdoctor,function($q){
                $q->orWhere('type','outdoctor');
            })
            ->latest()->get();
        }

        $models = $this->model->filter($request)->orderBy($request->order ? $request->order : 'updated_at', $request->sort ? $request->sort : 'DESC');

        if ($request->per_page) {
            $models = ['data' => $models->paginate($request->per_page), 'paginate' => true];
        } else {
            $models = ['data' => $models->get(), 'paginate' => false];
        }

        return responseJson(200, 'success', HMSDoctorResource::collection($models['data']), $models['paginate'] ? getPaginates($models['data']) : null);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(HMSDoctorRequest $request)
    {
        if($request->type == 'indoctor'){
            $employee = $this->create_employee($request);
            $user = $this->create_user($request,$employee->id);
        }

        $doctor = $this->model->create($this->doctor_data($request,$request->type == 'indoctor' ? $user->id : null,$request->type == 'indoctor' ?$employee->id : null));

        if($request->type == 'indoctor')
            $this->doctor_work_times($request->work_times,$doctor);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(HMSDoctorRequest $request, HMSDoctor $doctor)
    {
        $doctor_data = $request->only(['name','name_e','mobile','whatsapp','email','type','analytic_account','specialty_id','selling_items_commission','transfer_patient_commission','medical_commission']);
        $employee_data = $request->only(['name','name_e','mobile','whatsapp','company_id','code_country']);
        $user_data = $request->only(['name','name_e','email','company_id','is_active','password']);
        $doctor->update($doctor_data);
        if($request->type == 'indoctor'){
            if($doctor->user)
                $doctor->user->update($user_data);
            else{
                $user = $doctor->user()->create($user_data);
                $doctor->update(['user_id' => $user->id]);
            }
            if($doctor->employee)
                $doctor->employee->update($employee_data);
            else{
                $employee = $doctor->employee()->create($employee_data);
                $doctor->update(['employee_id' => $employee->id]);
            }

            $doctor->work_times()->forceDelete();
            $this->doctor_work_times($request->work_times,$doctor);
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(HMSDoctor $doctor)
    {
        if($doctor->user)
            $doctor->user->delete();
        $doctor->delete();
    }

    protected function doctor_data($request,$user_id,$employee_id){
        $data = $request->only(['name','name_e','mobile','whatsapp','email','analytic_account','type','specialty_id','selling_items_commission','transfer_patient_commission','medical_commission']);
        $data['user_id'] = $user_id;
        $data['employee_id'] = $employee_id;
        return $data;
    }


    protected function create_employee($request){
        return Employee::create($request->only(['name','name_e','mobile','whatsapp','company_id','code_country']));
    }

    protected function create_user($request,$employee_id){
        $data = $request->only(['name','name_e','email','company_id','is_active','password']);
        $data['employee_id'] = $employee_id;
        $data['type'] = 'doctor';
        return User::create($data);
    }

    protected function doctor_work_times($work_times,$doctor){
        foreach($work_times as $work_time)
            $doctor->work_times()->create($work_time);
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
