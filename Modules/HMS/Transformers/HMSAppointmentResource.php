<?php

namespace Modules\HMS\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class HMSAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $patient = $this->patient;
        $doctor = $this->doctor;
        $from_doctor = $this->from_doctor;
        $room = $this->room;
        $branch = $this->room->branch;

        return [
          "id" => $this->id,
          "date" => $this->date,
          "patient_id" => $this->patient_id,
          "doctor_id" => $this->doctor_id,
          "from_doctor_id" => $this->from_doctor_id,
          "room_id" => $this->room_id,
          "number" => $this->number,
          "reason" => $this->reason,
          "status" => $this->status,
          "number_of_invoices" => $this->invoices()->count(),
          "number_of_prescriptions" => $this->prescriptions()->count(),
          "created_at" => $this->created_at->format('Y-m-d ( H:i )'),
          "patient" =>$this->short_relation($patient),
          "doctor" =>$this->short_relation($doctor),
          "from_doctor" =>$this->when($from_doctor,$this->short_relation($from_doctor)),
          "room" =>$this->short_relation($room),
          "branch" =>$this->short_relation($branch),
          "final_time"=>[
            'start' => $this->start_time->start,
            'end' => $this->end_time->end,
          ],
        //   "times" =>HMSAppointmentTimesResource::collection($this->times),//all times
        ];
    }

    protected function short_relation($model){
        if(!$model)
            return [];

        return [
            "id" => $model->id,
            "name" => $model->name,
            "name_e" => $model->name_e,
        ];
    }
}
