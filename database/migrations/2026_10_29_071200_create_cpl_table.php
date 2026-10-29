<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpl', function (Blueprint $table) {
            $table->string('kode_cpl', 20)->primary();
            $table->text('deskripsi_cpl');
            $table->string('domain_kompetensi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpl');
    }
};
