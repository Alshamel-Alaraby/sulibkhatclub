<?php

namespace Modules\Booking\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnitStatus extends Model
{
    protected $table = 'booking_unit_statuses';
    protected $guarded = ['id'];

}
