<?php

namespace App\Http\Controllers;

use App\Models\PengaturanMonitor;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman selamat datang dengan data pengaturan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil pengaturan pertama dari tabel pengaturan_monitor
        $pengaturan = PengaturanMonitor::first();
        
        // Ubah nama kolom 'logo' menjadi 'logo_sekolah' agar sesuai dengan view
        $viewData = [];
        if ($pengaturan) {
            $viewData['logo_sekolah'] = $pengaturan->logo;
            // Anda bisa menambahkan data lain di sini jika perlu
        }

        return view('welcome', ['pengaturan' => $viewData]);
    }
}