<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\PengaturanMonitor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonitorController extends Controller
{
    /**
     * Menampilkan halaman utama monitor antrian.
     * View ini nantinya akan berisi Javascript untuk memanggil endpoint data().
     */
    public function index()
    {
        return view('monitor.index');
        // return "Ini adalah halaman untuk menampilkan monitor antrian. Halaman ini akan memanggil API untuk mendapatkan data real-time.";
    }

    /**
     * Menyediakan data real-time untuk halaman monitor.
     * Endpoint ini akan dipanggil secara periodik oleh Javascript.
     */
    public function data()
    {
        try {
            $today = Carbon::today();

            // 1. Dapatkan semua loket yang sedang buka
            $lokets = Loket::where('status', 'buka')->get();

            // 2. Dapatkan antrian yang sedang dipanggil di setiap loket
            $antrianDipanggil = [];
            foreach ($lokets as $loket) {
                $antrian = Antrian::where('id_loket', $loket->id)
                    ->whereDate('tanggal', $today)
                    ->where('status', 'dipanggil')
                    ->with('jenisAntrian')
                    ->orderBy('waktu_panggilan', 'desc')
                    ->first();

                // Lakukan pemeriksaan keamanan untuk memastikan relasi 'jenisAntrian' ada
                if ($antrian && $antrian->jenisAntrian) {
                    $antrianDipanggil[$loket->kode_loket] = [
                        'nomor_lengkap' => $antrian->jenisAntrian->kode_antrian . '-' . str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT),
                        'nama_loket' => $loket->nama_loket,
                    ];
                } else {
                    $antrianDipanggil[$loket->kode_loket] = null;
                }
            }

            // 3. Dapatkan antrian yang terakhir kali dipanggil secara keseluruhan (untuk efek suara)
            $panggilanTerbaru = Antrian::whereDate('tanggal', $today)
                ->whereNotNull('waktu_panggilan')
                ->with('jenisAntrian', 'loket')
                ->orderBy('waktu_panggilan', 'desc')
                ->first();

            // Siapkan data panggilan terbaru dengan aman untuk menghindari error serialisasi
            $panggilanTerbaruData = null;
            if ($panggilanTerbaru && $panggilanTerbaru->jenisAntrian && $panggilanTerbaru->loket) {
                $panggilanTerbaruData = [
                    'nomor_lengkap' => $panggilanTerbaru->jenisAntrian->kode_antrian . '-' . str_pad($panggilanTerbaru->nomor_antrian, 3, '0', STR_PAD_LEFT),
                    'nomor_antrian' => $panggilanTerbaru->nomor_antrian,
                    'kode_antrian' => $panggilanTerbaru->jenisAntrian->kode_antrian,
                    'nama_loket' => $panggilanTerbaru->loket->nama_loket,
                    'waktu_panggilan' => $panggilanTerbaru->waktu_panggilan->toIso8601String(),
                ];
            }

            // 4. Dapatkan daftar riwayat panggilan (misal: 5 terakhir)
            $riwayatPanggilan = Antrian::whereDate('tanggal', $today)
                ->whereIn('status', ['dipanggil', 'selesai'])
                ->with('jenisAntrian', 'loket')
                ->orderBy('waktu_panggilan', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    // Pemeriksaan untuk memastikan relasi tidak null
                    if (!$item->jenisAntrian || !$item->loket) {
                        return null;
                    }
                    return [
                        'nomor_lengkap' => $item->jenisAntrian->kode_antrian . '-' . str_pad($item->nomor_antrian, 3, '0', STR_PAD_LEFT),
                        'nama_loket' => $item->loket->nama_loket,
                    ];
                })
                ->filter() // Menghapus item yang null dari koleksi
                ->values(); // Mengindeks ulang koleksi setelah filter

            // 5. Dapatkan pengaturan monitor
            $pengaturan = PengaturanMonitor::first();
            $pengaturanData = $pengaturan ? [
                'running_text' => $pengaturan->running_text,
                'video_url' => $pengaturan->video ? asset('storage/' . $pengaturan->video) : null,
                'logo_url' => $pengaturan->logo ? asset('storage/' . $pengaturan->logo) : null,
            ] : null;

            return response()->json([
                'antrian_dipanggil' => $antrianDipanggil,
                'panggilan_terbaru' => $panggilanTerbaruData,
                'riwayat_panggilan' => $riwayatPanggilan,
                'pengaturan' => $pengaturanData,
            ]);
        } catch (\Throwable $e) {
            // Jika terjadi error, kirim response JSON dengan pesan error yang sebenarnya
            return response()->json([
                'error' => 'Terjadi kesalahan pada server.',
                'message' => $e->getMessage(), // Ini adalah pesan error yang sebenarnya
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}