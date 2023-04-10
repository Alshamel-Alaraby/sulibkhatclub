<?php

namespace App\Models;

use App\Traits\ConnTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function customTables()
    {
        return $this->hasMany(GeneralCustomTable::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function hasChildren()
    {
        return $this->stores()->count() > 0 ||
        $this->serials()->count() > 0
        ;
    }




}
