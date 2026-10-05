<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // create_access_requests_table
public function up(): void
{
    Schema::create('access_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('member_id')->nullable()->constrained();
        $table->string('plate_detected');
        $table->boolean('plate_match')->default(false);
        $table->boolean('face_match')->default(false);
        $table->enum('status', ['auto_approved', 'pending', 'approved_by_member', 'rejected_by_member', 'timeout', 'denied'])
              ->default('pending');
        $table->timestamp('requested_at');
        $table->timestamp('responded_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
