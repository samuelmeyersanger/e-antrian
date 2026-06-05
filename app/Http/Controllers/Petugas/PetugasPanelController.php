<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\JenisAntrian;
use App\Models\Loket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PetugasPanelController extends Controller
{
    /**
     * Menampilkan panel utama petugas.
     * Jika petugas tidak ditugaskan ke loket, tampilkan halaman pemberitahuan.
     */
    public function index()
    {
        $petugas = Auth::user();
        if (!$petugas->loket) {
            return view('petugas.unassigned');
        }
        return view('petugas.panel', [
            'petugas' => $petugas,
            'loket' => $petugas->loket
        ]);
    }

    /**
     * Helper untuk mendapatkan loket baik dari request (untuk admin) atau dari user (untuk petugas).
     */
    private function getLoketFromRequest(Request $request)
    {
        $user = Auth::user();
        // Jika user adalah admin dan mengirim loket_id, gunakan itu.
        if ($user->role == 'admin' && $request->has('loket_id')) {
            return Loket::find($request->input('loket_id'));
        }
        // Jika tidak, gunakan loket yang terhubung dengan petugas.
        return $user->loket;
    }

    /**
     * Mengambil state terkini dari antrian untuk panel petugas (via AJAX).
     */
    private function buildStateResponse(Loket $loket)
    {
        $today = Carbon::today();

        $antrianSaatIniModel = Antrian::where('id_loket', $loket->id)
            ->where('status', 'dipanggil')
            ->whereDate('tanggal', $today)
            ->first();

        $antrianSaatIniData = null;
        if ($antrianSaatIniModel) {
            $antrianSaatIniModel->load('jenisAntrian');
            $antrianSaatIniData = $antrianSaatIniModel->toArray();
        }

        $daftarTungguModel = Antrian::with('jenisAntrian')
            ->where('status', 'menunggu')
            ->whereDate('tanggal', $today)
            ->orderBy('nomor_antrian', 'asc')
            ->get();

        $totalDilayani = Antrian::where('id_loket', $loket->id)
            ->where('status', 'selesai')
            ->whereDate('tanggal', $today)
            ->count();

        $sisaAntrian = $daftarTungguModel->count();
        $jenisAntrianAktifModel = JenisAntrian::where('status', 'buka')->get();

        $responseData = [
            'antrian_saat_ini' => $antrianSaatIniData,
            'daftar_tunggu' => $daftarTungguModel->toArray(), // <-- PERBAIKAN
            'total_dilayani' => $totalDilayani,
            'sisa_antrian' => $sisaAntrian,
            'jenis_antrian_aktif' => $jenisAntrianAktifModel->toArray(), // <-- PERBAIKAN
        ];

        // -- LOGGING SISI SERVER --
        // Log::info('Membangun respons state untuk dikirim ke browser:', $responseData);

        return response()->json($responseData);
    }

    /**
     * Mengambil state terkini dari antrian untuk panel petugas (via AJAX).
     */
    public function getState(Request $request)
    {
        try {
            $loket = $this->getLoketFromRequest($request);
            if (!$loket) {
                return response()->json(['message' => 'Loket tidak ditemukan atau Anda tidak memiliki akses.'], 403);
            }
            return $this->buildStateResponse($loket);

        } catch (Throwable $th) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat mengambil data state.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Memanggil antrian berikutnya secara umum.
     */
    public function panggilBerikutnya(Request $request)
    {
        $loket = $this->getLoketFromRequest($request);
        if (!$loket) {
            return response()->json(['message' => 'Loket tidak valid.'], 400);
        }
        $today = Carbon::today();

        $result = DB::transaction(function () use ($loket, $today) {
            if ($this->adaAntrianAktifDiLoket($loket->id, $today)) {
                return ['success' => false, 'message' => 'Selesaikan antrian saat ini terlebih dahulu.', 'status' => 409];
            }

            $antrian = Antrian::where('status', 'menunggu')
                ->whereDate('tanggal', $today)
                ->orderBy('nomor_antrian', 'asc')
                ->lockForUpdate()
                ->first();

            if ($antrian) {
                $this->panggilAntrian($antrian, $loket->id);
                return ['success' => true];
            }

            return ['success' => false, 'message' => 'Tidak ada antrian untuk dipanggil.', 'status' => 404];
        });

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return $this->buildStateResponse($loket);
    }

    /**
     * Memanggil antrian berikutnya dari jenis layanan spesifik.
     */
    public function panggilSpesifik(Request $request)
    {
        $request->validate(['jenis_antrian_id' => 'required|exists:jenis_antrian,id']);

        $loket = $this->getLoketFromRequest($request);
        if (!$loket) {
            return response()->json(['message' => 'Loket tidak valid.'], 400);
        }
        $today = Carbon::today();

        $result = DB::transaction(function () use ($request, $loket, $today) {
            if ($this->adaAntrianAktifDiLoket($loket->id, $today)) {
                return ['success' => false, 'message' => 'Selesaikan antrian saat ini terlebih dahulu.', 'status' => 409];
            }

            $antrian = Antrian::where('status', 'menunggu')
                ->where('jenis_antrian_id', $request->jenis_antrian_id)
                ->whereDate('tanggal', $today)
                ->orderBy('nomor_antrian', 'asc')
                ->lockForUpdate()
                ->first();

            if ($antrian) {
                $this->panggilAntrian($antrian, $loket->id);
                return ['success' => true];
            }

            return ['success' => false, 'message' => 'Tidak ada antrian untuk jenis layanan ini.', 'status' => 404];
        });

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return $this->buildStateResponse($loket);
    }

    /**
     * Memanggil ulang antrian yang sedang aktif.
     */
    public function panggilUlang(Request $request)
    {
        $loket = $this->getLoketFromRequest($request);
        if (!$loket) {
            return response()->json(['message' => 'Loket tidak valid.'], 400);
        }

        $antrian = $this->getAntrianAktif($loket->id);
        if ($antrian) {
            $antrian->increment('jumlah_panggilan');
            $antrian->touch('waktu_panggilan');
            return $this->buildStateResponse($loket);
        }
        return response()->json(['message' => 'Tidak ada antrian aktif untuk dipanggil ulang.'], 404);
    }

    /**
     * Menyelesaikan antrian yang sedang dilayani.
     */
    public function selesai(Request $request)
    {
        $loket = $this->getLoketFromRequest($request);
        if (!$loket) {
            return response()->json(['message' => 'Loket tidak valid.'], 400);
        }

        $antrian = $this->getAntrianAktif($loket->id);
        if ($antrian) {
            $antrian->update([
                'status' => 'selesai',
                'waktu_selesai' => now(),
            ]);
            return $this->buildStateResponse($loket);
        }
        return response()->json(['message' => 'Tidak ada antrian aktif untuk diselesaikan.'], 404);
    }

    /**
     * Menandai antrian sebagai tidak hadir.
     */
    public function tidakHadir(Request $request)
    {
        $loket = $this->getLoketFromRequest($request);
        if (!$loket) {
            return response()->json(['message' => 'Loket tidak valid.'], 400);
        }

        $antrian = $this->getAntrianAktif($loket->id);
        if ($antrian) {
            $antrian->update(['status' => 'tidak-hadir']);
            return $this->buildStateResponse($loket);
        }
        return response()->json(['message' => 'Tidak ada antrian aktif untuk ditandai tidak hadir.'], 404);
    }

    // --- Helper Methods --- //

    /**
     * Helper untuk mendapatkan antrian yang sedang aktif di loket petugas.
     */
    private function getAntrianAktif($loketId)
    {
        return Antrian::where('id_loket', $loketId)
            ->where('status', 'dipanggil')
            ->whereDate('tanggal', Carbon::today())
            ->first();
    }

    /**
     * Helper untuk memeriksa apakah ada antrian aktif di sebuah loket.
     */
    private function adaAntrianAktifDiLoket($loketId, $tanggal)
    {
        return Antrian::where('id_loket', $loketId)
            ->where('status', 'dipanggil')
            ->whereDate('tanggal', $tanggal)
            ->exists();
    }

    /**
     * Helper untuk mengupdate status antrian menjadi 'dipanggil'.
     */
    private function panggilAntrian(Antrian $antrian, $loketId)
    {
        $antrian->update([
            'status' => 'dipanggil',
            'id_loket' => $loketId,
            'waktu_panggilan' => now(),
            'jumlah_panggilan' => 1,
        ]);
    }
}