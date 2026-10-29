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
        Schema::create('pkl_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pkl_period_id')
                ->constrained('pkl_periods')
                ->cascadeOnDelete();

            $table->string('nama_batch');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->enum('status', [
                'draft',
                'terjadwal',
                'dibuka',
                'ditutup'
            ])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_batches');
    }
};
