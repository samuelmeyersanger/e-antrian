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
        Schema::create('pengaturan_monitor', function (Blueprint $table) {
            $table->id();
            $table->string('warna_latar')->nullable();
            $table->string('warna_teks')->nullable();
            $table->string('font_teks')->nullable();
            $table->string('warna_font')->nullable();
            $table->integer('ukuran_teks')->nullable();
            $table->string('logo')->nullable();
            $table->string('video')->nullable();
            $table->string('running_text')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_monitor');
    }
};
