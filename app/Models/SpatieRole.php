<?php

namespace App\Models;

use App\Traits\LogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class SpatieRole extends Model
{
    protected $table = 'roles';
    public function hotkeys()
    {
        return $this->hasMany(HotkeyRole::class, 'role_id');
    }
}
