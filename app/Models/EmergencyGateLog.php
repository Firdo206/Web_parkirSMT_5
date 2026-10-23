<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyGateLog extends Model
{
    protected $fillable = ['gate', 'reason', 'issued_by'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}