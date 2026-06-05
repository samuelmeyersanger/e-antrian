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
        Schema::create('jenis_antrian_loket', function (Blueprint $table) {
            $table->foreignId('jenis_antrian_id')->constrained('jenis_antrian')->onDelete('cascade');
            $table->foreignId('loket_id')->constrained('loket')->onDelete('cascade');
            $table->primary(['jenis_antrian_id', 'loket_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_antrian_loket');
    }
};