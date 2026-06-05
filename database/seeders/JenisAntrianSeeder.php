<?php

namespace Database\Seeders;

use App\Models\JenisAntrian;
use Illuminate\Database\Seeder;

class JenisAntrianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisAntrian::create([
            'kode_antrian' => 'A',
            'nama_antrian' => 'Pendaftaran Ulang',
            'status' => 'buka',
        ]);

        JenisAntrian::create([
            'kode_antrian' => 'B',
            'nama_antrian' => 'Pengambilan Formulir',
            'status' => 'buka',
        ]);
    }
}
