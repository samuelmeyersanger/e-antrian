<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Antrian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'antrian';

    protected $fillable = [
        'jenis_antrian_id',
        'nomor_antrian',
        'tanggal',
        'status',
        'id_loket',
        'waktu_panggilan',
        'waktu_selesai',
        'jumlah_panggilan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_panggilan' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['jenisAntrian'];

    public function jenisAntrian()
    {
        return $this->belongsTo(JenisAntrian::class);
    }

    public function loket()
    {
        return $this->belongsTo(Loket::class, 'id_loket');
    }
}