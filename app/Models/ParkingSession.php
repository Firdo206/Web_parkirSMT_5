<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingSession extends Model
{
    protected $fillable = [
        'member_id',
        'vehicle_id',
        'entry_access_request_id',
        'exit_access_request_id',
        'entry_time',
        'exit_time',
        'status',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function entryAccessRequest(): BelongsTo
    {
        return $this->belongsTo(
            AccessRequest::class,
            'entry_access_request_id'
        );
    }

    public function exitAccessRequest(): BelongsTo
    {
        return $this->belongsTo(
            AccessRequest::class,
            'exit_access_request_id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}