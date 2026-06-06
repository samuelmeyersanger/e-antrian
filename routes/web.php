<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController; // Tambahkan ini
use Illuminate\Support\Facades\Route;
use App\Models\JenisAntrian;
use App\Models\Antrian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Menggunakan controller untuk halaman utama
Route::get('/', [WelcomeController::class, 'index']);

Route::get('/dashboard', function () {
    $today = Carbon::today();

    // 1. Ambil semua jenis layanan
    $jenisAntrians = JenisAntrian::all();

    // 2. Hitung statistik untuk setiap jenis layanan
    $statistik = $jenisAntrians->map(function ($jenis) use ($today) {
        $stats = Antrian::where('jenis_antrian_id', $jenis->id)
            ->whereDate('tanggal', $today)
            ->selectRaw("
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN status = 'tidak-hadir' THEN 1 ELSE 0 END) as tidak_hadir,
                COUNT(*) as total
            ")
            ->first();

        return [
            'nama' => $jenis->nama,
            'selesai' => (int) $stats->selesai,
            'tidak_hadir' => (int) $stats->tidak_hadir,
            'total' => (int) $stats->total,
        ];
    });

    // 3. Hitung total keseluruhan
    $totalKeseluruhan = [
        'selesai' => $statistik->sum('selesai'),
        'tidak_hadir' => $statistik->sum('tidak_hadir'),
        'total' => $statistik->sum('total'),
    ];

    return view('dashboard', [
        'statistik' => $statistik,
        'totalKeseluruhan' => $totalKeseluruhan,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| E-Antrian Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\JenisAntrianController;
use App\Http\Controllers\Admin\LoketController;
use App\Http\Controllers\Admin\PengaturanMonitorController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\Petugas\PetugasPanelController;

// Rute Publik (Ambil Antrian & Monitor)
Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
Route::post('/antrian', [AntrianController::class, 'store'])->name('antrian.store');
Route::get('/antrian/sukses/{id}', [AntrianController::class, 'success'])->name('antrian.success');
Route::get('/monitor', [MonitorController::class, 'index'])->name('monitor.index');
Route::get('/monitor/data', [MonitorController::class, 'data'])->name('monitor.data');


// Rute untuk Petugas (Memerlukan Login)
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/panel', [PetugasPanelController::class, 'index'])->name('panel.index');
    Route::get('/panel/state', [PetugasPanelController::class, 'getstate'])->name('panel.state');
    Route::post('/panel/panggil-berikutnya', [PetugasPanelController::class, 'panggilBerikutnya'])->name('panel.panggilBerikutnya');
    Route::post('/panel/panggil-spesifik', [PetugasPanelController::class, 'panggilSpesifik'])->name('panel.panggilSpesifik');
    Route::post('/panel/panggil-ulang', [PetugasPanelController::class, 'panggilUlang'])->name('panel.panggilUlang');
    Route::post('/panel/selesai', [PetugasPanelController::class, 'selesai'])->name('panel.selesai');
    Route::post('/panel/tidak-hadir', [PetugasPanelController::class, 'tidakHadir'])->name('panel.tidakHadir');
    Route::post('/panel/panggil-terlewat', [PetugasPanelController::class, 'panggilTerlewat'])->name('panel.panggilTerlewat');
});


// Rute untuk Admin (Memerlukan Login & Peran Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Mengarahkan dashboard admin ke manajemen loket
    Route::get('/dashboard', function() {
        return redirect()->route('admin.loket.index');
    })->name('dashboard');

    Route::resource('loket', LoketController::class);
    Route::resource('jenis-antrian', JenisAntrianController::class);
    Route::resource('petugas', PetugasController::class);

    // Pengaturan Monitor
    Route::get('pengaturan-monitor', [PengaturanMonitorController::class, 'index'])->name('pengaturan-monitor.index');
    Route::put('pengaturan-monitor/{id}', [PengaturanMonitorController::class, 'update'])->name('pengaturan-monitor.update');

    Route::get('panggilan', [AdminPanelController::class, 'panggilanPanel'])->name('panggilan');
});