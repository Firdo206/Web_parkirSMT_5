<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('emergency_gate_logs', function (Blueprint $table) {
        $table->id();
        $table->string('gate');
        $table->string('reason');
        $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('emergency_gate_logs');
}
};
