<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'action',
        'message',
        'task_id',
    ];
    protected $table = "general_tasks";

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
