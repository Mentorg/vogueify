<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'iso_code'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
