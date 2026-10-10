<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberPayment extends Model
{
    protected $fillable = [
        'member_subscription_id',
        'amount_due',
        'amount_paid',
        'method',
        'paid_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(MemberSubscription::class, 'member_subscription_id');
    }
}