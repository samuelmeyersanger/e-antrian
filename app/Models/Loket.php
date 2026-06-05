<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loket';

    protected $fillable = [
        'jenis_antrian_id',
        'kode_loket',
        'nama_loket',
        'status',
    ];

    public function jenisAntrian()
    {
        return $this->belongsTo(JenisAntrian::class);
    }

    public function antrian()
    {
        return $this->hasMany(Antrian::class, 'id_loket');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}