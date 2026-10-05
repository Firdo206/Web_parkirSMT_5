<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingGateState extends Model{
    protected $fillable=[
        'gate',
        'trigger',
        'izin',
        'status',
    ];

    protected $casts=[
        'trigger'=>'boolean',
    ];
}