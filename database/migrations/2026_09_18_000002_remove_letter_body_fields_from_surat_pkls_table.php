<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_pkls', function (Blueprint $table) {
            $table->dropColumn(['pembuka_surat', 'isi_surat', 'penutup_surat']);
        });
    }

    public function down(): void
    {
        Schema::table('surat_pkls', function (Blueprint $table) {
            $table->text('pembuka_surat')->nullable();
            $table->text('isi_surat')->nullable();
            $table->text('penutup_surat')->nullable();
        });
    }
};
