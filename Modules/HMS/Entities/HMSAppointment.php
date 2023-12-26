<?php

namespace Modules\HMS\Entities;


use App\Models\Branch;
use App\Models\DocumentHeader;
use App\Models\User;
use App\Traits\LogTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class HMSAppointment extends Model
{
    use HasFactory, LogTrait, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['time' => 'json'];

    public function patient()
    {
        return $this->belongsTo(HMSPatient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(HMSDoctor::class, 'doctor_id');
    }

    public function from_doctor()
    {
        return $this->belongsTo(HMSDoctor::class, 'from_doctor_id');
    }

    public function room()
    {
        return $this->belongsTo(HMSRoom::class, 'room_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(HMSPrescription::class, 'appointment_id');
    }

    public function invoices()
    {
        return $this->hasMany(DocumentHeader::class, 'appointment_id');
    }

    public function start_time()
    {
        return $this->hasOne(HMSAppointmentTime::class, 'appointment_id')->ofMany('start', 'MIN');
    }
    public function end_time()
    {
        return $this->hasOne(HMSAppointmentTime::class, 'appointment_id')->ofMany('end', 'MAX');
    }
    public function times()
    {
        return $this->hasMany(HMSAppointmentTime::class, 'appointment_id');
    }

    public function scopeCheckThisTimeIsExistsBeforeForThisDoctorInThisRoom($q, $date, $doctor_id, $room_id, $start, $end)
    {
        $q->join('h_m_s_appointment_times', 'h_m_s_appointment_times.appointment_id', 'h_m_s_appointments.id')
            ->where('date', $date)->whereDoctorId($doctor_id)
            ->whereRoomId($room_id)->where('status', '!=', 'Cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q) use ($start, $end) {
                    $q->where('h_m_s_appointment_times.start', '<=', $start)->where('h_m_s_appointment_times.end', '>', $start);
                })
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('h_m_s_appointment_times.start', '<', $end)->where('h_m_s_appointment_times.end', '>=', $end);
                    });
            });
    }


    protected static function booted()
    {
        static::addGlobalScope(function ($query) {
            $user = request()->user();
            if ($user && $user->type == 'doctor') {
                $query->where('doctor_id', $user->doctor->id);
            }
        });
    }


    public function scopeGetAppointmentsThroughMonthGroubByWeeks($q,$year,$month){
        return $q
        ->where('status','!=','Cancelled')
        ->whereYear('date',$year)
        ->whereMonth('date',$month)
        ->selectRaw('WEEK(date) as week, COUNT(*) as count')
        ->groupBy('week')
        ->get()
        ->map(function ($item) {
            $item->week = $this->getNameOfWeekInMounth(Carbon::now()->startOfMonth()->addWeeks($item->week - 1)->format('d'));
            return $item;
        })->groupBy('week')->map(function ($item) {
           return ['week' => $item[0]->week,'count' => $item->sum('count')];
        })->values();
    }

    public function getNameOfWeekInMounth($week_number)
    {
        switch(true){
            case ($week_number <= 7):
                $week_name = 'First Week';
                break;
            case ($week_number <= 15):
                $week_name = 'Second Week';
                break;
            case ($week_number <= 22):
                $week_name = 'Third Week';
                break;
            case ($week_number <= 29):
                $week_name = 'Fourth Week';
                break;
            case ($week_number <= 31):
                $week_name = 'Fifth Week';
                break;
        }
        return $week_name;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $user = auth()->user()->id ?? "system";

        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->useLogName('Appointment')
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName} by ($user)");
    }
}
