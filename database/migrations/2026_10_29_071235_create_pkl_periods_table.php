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
        Schema::create('pkl_periods', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('tahun_akademik', 9);
            $table->enum('status', [
                'draft',
                'aktif',
                'selesai'
            ])->default('draft');
            $table->timestamps();

            $table->unique(
                ['semester', 'tahun_akademik'],
                'pkl_periods_semester_tahun_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_periods');
    }
};
