<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityAlert extends Model
{
    protected $fillable = ['access_request_id', 'type', 'status', 'handled_by'];

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}