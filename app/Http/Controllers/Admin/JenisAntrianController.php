<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisAntrian;
use Illuminate\Http\Request;

class JenisAntrianController extends Controller
{
    public function index()
    {
        $jenisAntrian = JenisAntrian::latest()->get();
        return view('admin.jenis-antrian.index', compact('jenisAntrian'));
    }

    public function create()
    {
        return view('admin.jenis-antrian.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_antrian' => 'required|string|max:255',
            'kode_antrian' => 'required|string|max:5|unique:jenis_antrian,kode_antrian',
            'status' => 'required|in:buka,tutup',
        ]);

        JenisAntrian::create($validated);

        return redirect()->route('admin.jenis-antrian.index')->with('success', 'Jenis Antrian berhasil ditambahkan.');
    }

    public function edit(JenisAntrian $jenisAntrian)
    {
        return view('admin.jenis-antrian.edit', compact('jenisAntrian'));
    }

    public function update(Request $request, JenisAntrian $jenisAntrian)
    {
        $validated = $request->validate([
            'nama_antrian' => 'required|string|max:255',
            'kode_antrian' => 'required|string|max:5|unique:jenis_antrian,kode_antrian,' . $jenisAntrian->id,
            'status' => 'required|in:buka,tutup',
        ]);

        $jenisAntrian->update($validated);

        return redirect()->route('admin.jenis-antrian.index')->with('success', 'Jenis Antrian berhasil diperbarui.');
    }

    public function destroy(JenisAntrian $jenisAntrian)
    {
        // Tambahkan validasi jika jenis antrian sudah pernah digunakan
        if ($jenisAntrian->antrians()->exists()) {
            return back()->withErrors(['error' => 'Jenis antrian ini tidak dapat dihapus karena sudah memiliki data antrian terkait.']);
        }
        
        $jenisAntrian->delete();
        return redirect()->route('admin.jenis-antrian.index')->with('success', 'Jenis Antrian berhasil dihapus.');
    }
}