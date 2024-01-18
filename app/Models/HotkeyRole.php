<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotkeyRole extends Model
{
    protected $table = 'hotkey_role';
    protected $guarded = ['id'];
    protected $fillable = ['column_name', 'table','can_see','can_edit','can_plus','percentage','role_id'];

}
