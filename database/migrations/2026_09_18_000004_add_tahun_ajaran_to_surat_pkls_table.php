<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_pkls', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->nullable()->after('nama_kelas');
        });
    }

    public function down(): void
    {
        Schema::table('surat_pkls', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran');
        });
    }
};
