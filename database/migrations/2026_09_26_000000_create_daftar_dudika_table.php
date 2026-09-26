<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_dudika', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dudika');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_dudika');
    }
};
