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
        'price_motor' => 'decimal:2',
        'price_mobil' => 'decimal:2',
        'features'    => 'array',
        'is_popular'  => 'boolean',
        'is_active'   => 'boolean',
    ];

    // Slug otomatis dibuat dari nama kalau masih kosong
    protected static function booted(): void
    {
        static::saving(function (SubscriptionPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    // Label durasi, misal "1 Bulan" atau "45 hari"
    public function getDurationLabelAttribute(): string
    {
        return match (true) {
            $this->duration_days == 7   => '1 Minggu',
            $this->duration_days == 30  => '1 Bulan',
            $this->duration_days == 365 => '1 Tahun',
            default => $this->duration_days . ' hari',
        };
    }

    // Daftar fitur sebagai array bersih.
    // Mendukung 2 format: array/JSON (versi baru) dan teks satu baris per fitur (versi lama).
    public function getFeaturesListAttribute(): array
    {
        $raw = $this->attributes['features'] ?? null;

        if (empty($raw)) {
            return [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                $raw = $decoded;
            } elseif (is_string($decoded)) {
                $raw = $decoded;
            }
        }

        $lines = is_array($raw) ? $raw : explode("\n", (string) $raw);

        return collect($lines)
            ->map(fn ($line) => trim((string) $line))
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
        return $query->orderBy('sort_order')
            ->orderBy('duration_days');
    }

    public function subscriptions()
    {
        return $this->hasMany(
            MemberSubscription::class,
            'subscription_plan_id'
        );
    }
}