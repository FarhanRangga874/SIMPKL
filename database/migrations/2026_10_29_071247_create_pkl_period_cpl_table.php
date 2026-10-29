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
        Schema::create('pkl_period_cpl', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pkl_period_id')
                ->constrained('pkl_periods')
                ->cascadeOnDelete();

            $table->string('kode_cpl', 20);

            $table->foreign('kode_cpl')
                ->references('kode_cpl')
                ->on('cpl')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['pkl_period_id', 'kode_cpl'],
                'pkl_period_cpl_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl_period_cpl');
    }
};
