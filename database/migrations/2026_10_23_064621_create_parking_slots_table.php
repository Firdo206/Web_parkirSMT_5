<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type', 10)->unique(); // mobil | motor
            $table->unsignedInteger('capacity')->default(0);
            $table->timestamps();
        });

        // Nilai awal; bisa diubah dari halaman Slot Parkir.
        DB::table('parking_slots')->insert([
            ['vehicle_type' => 'mobil', 'capacity' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'motor', 'capacity' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_slots');
    }
};