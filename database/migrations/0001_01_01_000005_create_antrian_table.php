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
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_antrian_id')->constrained('jenis_antrian');
            $table->string('nomor_antrian');
            $table->date('tanggal')->default(now());
            $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'tidak-hadir'])->default('menunggu');
            $table->foreignId('id_loket')->nullable()->constrained('loket');
            $table->datetime('waktu_panggilan')->nullable();
             $table->timestamp('waktu_selesai')->nullable();
            $table->unsignedInteger('jumlah_panggilan')->default(0)->comment('Melacak berapa kali nomor dipanggil');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['jenis_antrian_id', 'nomor_antrian', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};