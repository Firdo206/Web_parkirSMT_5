<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'duration_days',
        'price_motor',
        'price_mobil',
        'description',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_motor'  => 'decimal:2',
        'price_mobil'  => 'decimal:2',
        'is_popular'   => 'boolean',
        'is_active'    => 'boolean',
    ];

    // Slug otomatis dibuat dari nama tiap kali disimpan
    protected static function booted(): void
    {
        static::saving(function (SubscriptionPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    // Helper buat nampilin label durasi, misal "30 hari" atau "1 Tahun"
    public function getDurationLabelAttribute(): string
    {
        return match (true) {
            $this->duration_days == 7   => '1 Minggu',
            $this->duration_days == 30  => '1 Bulan',
            $this->duration_days == 365 => '1 Tahun',
            default => $this->duration_days . ' hari',
        };
    }

    // Pecah isi kolom `features` (satu baris per fitur) jadi array bersih
    public function getFeaturesListAttribute(): array
    {
        if (empty($this->features)) {
            return [];
        }

        return collect(explode("\n", $this->features))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('duration_days');
    }
}