<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pkls', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->nullable();
            $table->string('sifat')->nullable();
            $table->string('lampiran')->nullable();
            $table->string('perihal')->nullable();
            $table->string('pejabat_tujuan_surat')->nullable();
            $table->string('kota_tujuan_surat')->nullable();
            $table->text('pembuka_surat')->nullable();
            $table->text('isi_surat')->nullable();
            $table->text('penutup_surat')->nullable();
            $table->string('tempat_ditetapkan')->nullable();
            $table->date('tanggal_ditetapkan')->nullable();
            $table->foreignId('penandatangan_id')->nullable()->constrained('asns')->nullOnDelete();
            $table->integer('jumlah_siswa')->nullable();
            $table->string('jenis_instansi_yang_dituju')->nullable();
            $table->integer('lama_magang')->nullable();
            $table->date('tgl_awal_magang')->nullable();
            $table->date('tgl_akhir_magang')->nullable();
            $table->string('no_contact')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pkls');
    }
};
