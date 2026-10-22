<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Riwayat training model LBPH (opsional, berguna untuk audit & laporan sistem cerdas). */
    public function up(): void
    {
        Schema::create('face_model_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('version')->unique();
            $table->unsignedInteger('member_count');
            $table->unsignedInteger('trained_images');
            $table->decimal('threshold', 8, 2);              // UNKNOWN_THRESHOLD saat model dilatih
            $table->string('trigger', 30)->default('enroll'); // enroll | delete | retrain
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('trained_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_model_versions');
    }
};
