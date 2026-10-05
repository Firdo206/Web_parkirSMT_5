<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceProfile extends Model
{
    protected $fillable = ['member_id', 'embedding', 'photo_path'];

    protected function casts(): array
    {
        return ['embedding' => 'array'];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}