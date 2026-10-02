<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $fillable = [
        'member_id', 'plate_detected', 'plate_match', 'face_match',
        'status', 'requested_at', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'plate_match' => 'boolean',
            'face_match' => 'boolean',
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function securityAlert()
    {
        return $this->hasOne(SecurityAlert::class);
    }
}