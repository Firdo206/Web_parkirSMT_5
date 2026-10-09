<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'phone', 'email', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function faceProfiles()
    {
        return $this->hasMany(FaceProfile::class);
    }

    public function faceProfile()
    {
        return $this->hasOne(FaceProfile::class)->latestOfMany();
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class);
    }

    public function latestSubscription()
    {
        return $this->hasOne(MemberSubscription::class)->latestOfMany('end_date');
    }

    public function parkingSessions()
    {
        return $this->hasMany(ParkingSession::class);
    }
}