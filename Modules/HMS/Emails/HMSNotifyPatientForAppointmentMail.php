<?php

namespace Modules\HMS\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HMSNotifyPatientForAppointmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $name;

    public function __construct($message,$name)
    {
        $this->message=$message;
        $this->name=$name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('hms::emails.h_m_s_notify_patient_for_appointment',['message'=>$this->message,'name' => $this->name]);
    }
}
