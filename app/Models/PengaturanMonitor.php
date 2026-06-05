<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengaturanMonitor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengaturan_monitor';

    protected $fillable = [
        'warna_latar',
        'warna_teks',
        'font_teks',
        'warna_font',
        'ukuran_teks',
        'logo',
        'video',
        'running_text',
    ];
}
