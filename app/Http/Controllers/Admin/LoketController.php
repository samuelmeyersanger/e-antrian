<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisAntrian;
use App\Models\Loket;
use Illuminate\Http\Request;

class LoketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loket = Loket::latest()->get();
        return view('admin.loket.index', compact('loket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisAntrian = JenisAntrian::all();
        return view('admin.loket.create', compact('jenisAntrian'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_loket' => 'required|string|max:10|unique:loket,kode_loket',
            'nama_loket' => 'required|string|max:255',
            'status' => 'required|in:buka,tutup',
            'jenis_antrian_id' => 'required|exists:jenis_antrian,id',
        ]);

        Loket::create($validated);

        return redirect()->route('admin.loket.index')->with('success', 'Loket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loket $loket)
    {
        // Tidak digunakan untuk CRUD sederhana
        return redirect()->route('admin.loket.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loket $loket)
    {
        $jenisAntrian = JenisAntrian::all();
        return view('admin.loket.edit', compact('loket', 'jenisAntrian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loket $loket)
    {
        $validated = $request->validate([
            'kode_loket' => 'required|string|max:10|unique:loket,kode_loket,' . $loket->id,
            'nama_loket' => 'required|string|max:255',
            'status' => 'required|in:buka,tutup',
            'jenis_antrian_id' => 'required|exists:jenis_antrian,id',
        ]);

        $loket->update($validated);

        return redirect()->route('admin.loket.index')->with('success', 'Loket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loket $loket)
    {
        if ($loket->antrians()->exists() || $loket->users()->exists()) {
            return back()->withErrors(['error' => 'Loket ini tidak dapat dihapus karena sudah memiliki data antrian atau petugas terkait.']);
        }

        $loket->delete();
        return redirect()->route('admin.loket.index')->with('success', 'Loket berhasil dihapus.');
    }
}