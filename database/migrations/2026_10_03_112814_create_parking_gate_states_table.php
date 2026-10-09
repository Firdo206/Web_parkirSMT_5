<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::create('parking_gate_states',function(Blueprint $table){
            $table->id();
            $table->string('gate')->default('gate_utama');
            $table->boolean('trigger')->default(false);
            $table->string('izin')->default('SIAGA');
            $table->string('status')->default('SIAGA');
            $table->timestamps();
        });
    }

    public function down(): void{
        Schema::dropIfExists('parking_gate_states');
    }
};