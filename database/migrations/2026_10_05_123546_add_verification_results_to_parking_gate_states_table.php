<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->boolean('face_match')->default(false)->after('status');
            $table->unsignedBigInteger('face_member_id')->nullable()->after('face_match');
            $table->boolean('plate_match')->default(false)->after('face_member_id');
            $table->unsignedBigInteger('plate_member_id')->nullable()->after('plate_match');
            $table->string('plate_detected')->nullable()->after('plate_member_id');
        });
    }

    public function down(): void
    {
        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->dropColumn([
                'face_match',
                'face_member_id',
                'plate_match',
                'plate_member_id',
                'plate_detected',
            ]);
        });
    }
};