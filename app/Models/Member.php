<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function faceProfiles()
    {
        return $this->hasMany(FaceProfile::class);
    }

    public function accessRequests()
    {
        return $this->hasMany(AccessRequest::class);
    }
}