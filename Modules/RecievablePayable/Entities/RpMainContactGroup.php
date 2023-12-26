<?php

namespace Modules\RecievablePayable\Entities;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RpMainContactGroup extends Model
{
    use HasFactory,LogTrait;

    protected $guarded = ['id'];

  
}
