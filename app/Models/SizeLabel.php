<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeLabel extends Model
{
    public $timestamps = false;

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
