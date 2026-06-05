<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\JenisAntrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AntrianController extends Controller
{
    /**
     * Menampilkan halaman untuk memilih jenis layanan (ambil antrian).
     */
    public function index()
    {
        // Ambil semua jenis antrian yang aktif (status 'buka') untuk ditampilkan
        $jenisAntrian = JenisAntrian::where('status', 'buka')->get();
        return view('antrian.index', compact('jenisAntrian'));
    }

    /**
     * Menyimpan antrian baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_antrian_id' => 'required|exists:jenis_antrian,id',
        ]);

        $jenisAntrianId = $request->input('jenis_antrian_id');
        $today = Carbon::today();

        // Gunakan transaksi database untuk mencegah race condition (dua orang dapat nomor sama)
        $antrian = DB::transaction(function () use ($jenisAntrianId, $today) {
            // Kunci baris terakhir untuk jenis antrian dan tanggal ini untuk mencegah race condition
            $antrianTerakhir = Antrian::where('jenis_antrian_id', $jenisAntrianId)
                ->whereDate('tanggal', $today)
                ->orderBy('nomor_antrian', 'desc')
                ->lockForUpdate()
                ->first();

            $nomorBaru = $antrianTerakhir ? $antrianTerakhir->nomor_antrian + 1 : 1;

            // Buat antrian baru
            return Antrian::create([
                'jenis_antrian_id' => $jenisAntrianId,
                'nomor_antrian' => $nomorBaru,
                'tanggal' => $today,
                'status' => 'menunggu',
            ]);
        });

        // Redirect ke halaman sukses dengan membawa ID antrian
        return redirect()->route('antrian.success', ['id' => $antrian->id]);
    }

    /**
     * Menampilkan halaman tiket setelah berhasil mengambil nomor.
     */
    public function success($id)
    {
        // Cari antrian berdasarkan ID, dan eager load relasi jenisAntrian untuk efisiensi
        $antrian = Antrian::with('jenisAntrian')->findOrFail($id);
        return view('antrian.success', compact('antrian'));
    }
}