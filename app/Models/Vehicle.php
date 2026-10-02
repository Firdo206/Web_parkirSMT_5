<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['member_id', 'plate_number', 'vehicle_type'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}