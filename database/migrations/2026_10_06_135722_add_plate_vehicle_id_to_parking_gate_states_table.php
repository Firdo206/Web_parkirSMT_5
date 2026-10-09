<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->unsignedBigInteger('plate_vehicle_id')
                ->nullable()
                ->after('plate_member_id');
        });
    }

    public function down(): void
    {
        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->dropColumn('plate_vehicle_id');
        });
    }
};