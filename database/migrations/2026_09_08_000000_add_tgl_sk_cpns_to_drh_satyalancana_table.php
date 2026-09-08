<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drh_satyalancana', function (Blueprint $table) {
            $table->date('tgl_sk_cpns')->nullable()->after('no_sk_cpns');
        });
    }

    public function down(): void
    {
        Schema::table('drh_satyalancana', function (Blueprint $table) {
            $table->dropColumn('tgl_sk_cpns');
        });
    }
};
