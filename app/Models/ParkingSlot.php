<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    protected $fillable = ['vehicle_type', 'capacity'];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }
}