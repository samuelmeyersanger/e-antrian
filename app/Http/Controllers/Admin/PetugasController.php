<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PetugasController extends Controller
{
    public function index()
    {
        // Hanya ambil user dengan peran 'petugas'
        $petugas = User::where('role', 'petugas')->with('loket')->latest()->get();
        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        $loket = Loket::all();
        return view('admin.petugas.create', compact('loket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'loket_id' => ['nullable', 'exists:loket,id'],
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'petugas', // Otomatis set peran sebagai petugas
            'loket_id' => $request->loket_id,
        ]);

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit(User $petugas) // Laravel akan otomatis resolve $petugas dari route model binding
    {
        $loket = Loket::all();
        return view('admin.petugas.edit', [
            'petugas' => $petugas,
            'loket' => $loket
        ]);
    }

    public function update(Request $request, User $petugas)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email,'.$petugas->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'loket_id' => ['nullable', 'exists:loket,id'],
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'loket_id' => $request->loket_id,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas.index')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(User $petugas)
    {
        // Tambahkan validasi agar admin tidak bisa menghapus diri sendiri jika iseng
        if ($petugas->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $petugas->delete();
        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}