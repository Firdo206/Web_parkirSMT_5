<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table) {
            $table->string('gate', 50)->nullable()->after('member_id');
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('gate');
            $table->string('direction', 10)->nullable()->after('vehicle_id'); // entry | exit
            $table->string('reason', 30)->nullable()->after('direction');     // face_unknown | face_other_member
            $table->timestamp('expires_at')->nullable();                      // batas waktu respons pemilik
            $table->timestamp('buzzer_at')->nullable();                       // kapan buzzer terakhir diminta

            $table->index(['member_id', 'requested_at']);
        });

        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->timestamp('buzzer_until')->nullable(); // buzzer menyala sampai waktu ini
        });

        // Penyesuaian khusus MySQL/MariaDB (Laragon). Kolom lama dibaca dulu, hanya diubah kalau perlu.
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            $kolom = collect(Schema::getColumns('access_requests'))->keyBy('name');

            // Kalau status berupa ENUM, nilai baru seperti 'pending' bisa ditolak database.
            // Diubah jadi VARCHAR; nilai lama tetap aman.
            if ($kolom->has('status') && str_starts_with(strtolower($kolom['status']['type']), 'enum')) {
                DB::statement("ALTER TABLE access_requests MODIFY status VARCHAR(30) NOT NULL DEFAULT 'pending'");
            }

            // Permintaan yang masih menunggu belum punya waktu respons.
            if ($kolom->has('responded_at') && ! $kolom['responded_at']['nullable']) {
                DB::statement('ALTER TABLE access_requests MODIFY responded_at TIMESTAMP NULL');
            }
        }
    }

    public function down(): void
    {
        Schema::table('parking_gate_states', function (Blueprint $table) {
            $table->dropColumn('buzzer_until');
        });

        Schema::table('access_requests', function (Blueprint $table) {
            $table->dropIndex(['member_id', 'requested_at']);
            $table->dropColumn(['gate', 'vehicle_id', 'direction', 'reason', 'expires_at', 'buzzer_at']);
        });
    }
};