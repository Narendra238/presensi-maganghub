<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Kolom status kehadiran: hadir, tidak_hadir_dengan_keterangan, tidak_hadir_tanpa_keterangan
            $table->string('kehadiran')->default('hadir')->after('tanggal');

            // Kolom keterangan/alasan (hanya diisi saat tidak hadir dengan keterangan)
            $table->text('keterangan')->nullable()->after('kehadiran');

            // Ubah uraian_aktivitas & pembelajaran jadi nullable
            // (karena tidak diisi saat status tidak hadir)
            $table->text('uraian_aktivitas')->nullable()->change();
            $table->text('pembelajaran')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn(['kehadiran', 'keterangan']);

            $table->text('uraian_aktivitas')->nullable(false)->change();
            $table->text('pembelajaran')->nullable(false)->change();
        });
    }
};
