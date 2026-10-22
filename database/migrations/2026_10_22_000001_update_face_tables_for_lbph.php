<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. face_profiles: embedding dibuang, model LBPH sekarang disimpan di Python (data/models/lbph.yml)
        Schema::table('face_profiles', function (Blueprint $table) {
            $table->dropColumn('embedding');
            $table->unsignedSmallInteger('sample_count')->default(0)->after('photo_path'); // jumlah citra training (setelah augmentasi)
            $table->timestamp('trained_at')->nullable()->after('sample_count');           // kapan wajah ini masuk training
            $table->unique('member_id');                                                   // 1 member = 1 profil wajah
        });

        // 2. Jarak LBPH (makin kecil makin mirip) disimpan untuk kalibrasi threshold & bukti audit
        Schema::table('access_requests', function (Blueprint $table) {
            $table->decimal('face_distance', 8, 2)->nullable()->after('face_match');
        });

        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->decimal('face_distance', 8, 2)->nullable()->after('face_member_id');
        });

        // 3. Controller menangkap UniqueConstraintViolationException untuk No. HP, tapi indeks unik-nya belum ada
        Schema::table('members', function (Blueprint $table) {
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique(['phone']);
        });

        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->dropColumn('face_distance');
        });

        Schema::table('access_requests', function (Blueprint $table) {
            $table->dropColumn('face_distance');
        });

        Schema::table('face_profiles', function (Blueprint $table) {
            $table->dropUnique(['member_id']);
            $table->dropColumn(['sample_count', 'trained_at']);
            $table->json('embedding')->nullable()->after('member_id');
        });
    }
};
