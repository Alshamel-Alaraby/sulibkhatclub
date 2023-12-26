<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTypeDocument extends Model
{
    use HasFactory;

    protected $table = 'general_client_types_documents';

    protected $guarded = ['id'];
}
