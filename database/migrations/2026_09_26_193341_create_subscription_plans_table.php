<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // "Mingguan", "Bulanan", "Tahunan"
            $table->string('slug')->unique();               // "mingguan", "bulanan", "tahunan"
            $table->unsignedInteger('duration_days');       // 7, 30, 365, dst (bebas custom)
            $table->decimal('price_motor', 12, 2);          // harga untuk kendaraan motor
            $table->decimal('price_mobil', 12, 2);          // harga untuk kendaraan mobil
            $table->text('description')->nullable();        // catatan singkat, opsional
            $table->boolean('is_popular')->default(false);  // buat kasih badge "Rekomendasi"
            $table->boolean('is_active')->default(true);    // tampil/tidak ke user
            $table->unsignedInteger('sort_order')->default(0); // urutan tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};