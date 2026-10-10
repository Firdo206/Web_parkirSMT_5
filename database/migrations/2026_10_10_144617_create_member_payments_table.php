<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_subscription_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_due', 12, 2);   // tagihan yang dihitung server
            $table->decimal('amount_paid', 12, 2);  // nominal yang benar-benar dibayar
            $table->enum('method', ['tunai', 'transfer']);
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // id admin yang mencatat
            $table->timestamps();

            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_payments');
    }
};