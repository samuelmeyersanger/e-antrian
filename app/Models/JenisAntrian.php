<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisAntrian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_antrian';

    protected $fillable = [
        'kode_antrian',
        'nama_antrian',
        'status',
    ];
}
