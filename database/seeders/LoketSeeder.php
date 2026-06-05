<?php

namespace Database\Seeders;

use App\Models\Loket;
use Illuminate\Database\Seeder;

class LoketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Loket::create([
            'jenis_antrian_id' => 1, // Menghubungkan ke Jenis Antrian 1
            'kode_loket' => 'LKT-01',
            'nama_loket' => 'Loket 1',
            'status' => 'buka',
        ]);

        Loket::create([
            'jenis_antrian_id' => 2, // Menghubungkan ke Jenis Antrian 2
            'kode_loket' => 'LKT-02',
            'nama_loket' => 'Loket 2',
            'status' => 'buka',
        ]);
    }
}