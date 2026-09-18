<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_pkls', function (Blueprint $table) {
            $table->string('nama_kelas', 100)->nullable()->after('jumlah_siswa');
            $table->string('nama_jurusan', 255)->nullable()->after('nama_kelas');
        });
    }

    public function down(): void
    {
        Schema::table('surat_pkls', function (Blueprint $table) {
            $table->dropColumn(['nama_kelas', 'nama_jurusan']);
        });
    }
};
