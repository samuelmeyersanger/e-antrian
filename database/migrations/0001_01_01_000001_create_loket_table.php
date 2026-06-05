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
        Schema::create('loket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_antrian_id')->constrained('jenis_antrian')->onDelete('cascade');
            $table->string('kode_loket')->unique();
            $table->string('nama_loket')->nullable();
            $table->enum('status', ['buka', 'tutup'])->default('buka');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loket');
    }
};