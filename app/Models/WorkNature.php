<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkNature extends Model
{
    use HasFactory,SoftDeletes,LogTrait;

    protected $table = "general_work_natures";
    protected $guarded = ["id"];

}
