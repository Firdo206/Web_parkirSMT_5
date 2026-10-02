<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_security_alerts_table
public function up(): void
{
    Schema::create('security_alerts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('access_request_id')->constrained();
        $table->enum('type', ['face_mismatch_rejected', 'timeout_no_response']);
        $table->enum('status', ['open', 'resolved'])->default('open');
        $table->foreignId('handled_by')->nullable()->constrained('users');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_alerts');
    }
};
