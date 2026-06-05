<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanMonitorController extends Controller
{
    /**
     * Display the monitor settings form.
     */
    public function index()
    {
        // Get the first record, or create a new one if it doesn't exist
        $pengaturan = PengaturanMonitor::firstOrCreate([]);
        return view('admin.pengaturan-monitor.index', compact('pengaturan'));
    }

    /**
     * Update the monitor settings.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'warna_latar' => 'nullable|string|max:7',
            'warna_teks' => 'nullable|string|max:7',
            'font_teks' => 'nullable|string|max:255',
            'ukuran_teks' => 'nullable|integer|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20000',
            'running_text' => 'nullable|string',
        ]);

        $pengaturan = PengaturanMonitor::findOrFail($id);
        $data = $request->except(['logo', 'video']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($pengaturan->logo && Storage::disk('public')->exists($pengaturan->logo)) {
                Storage::disk('public')->delete($pengaturan->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            // Delete old video if it exists
            if ($pengaturan->video && Storage::disk('public')->exists($pengaturan->video)) {
                Storage::disk('public')->delete($pengaturan->video);
            }
            $data['video'] = $request->file('video')->store('videos', 'public');
        }

        $pengaturan->update($data);

        return redirect()->route('admin.pengaturan-monitor.index')->with('success', 'Pengaturan monitor berhasil diperbarui.');
    }
}
