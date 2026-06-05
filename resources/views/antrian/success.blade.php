<x-guest-layout>
    <div id="tiket-antrian" class="w-full max-w-sm mx-auto bg-white p-6 border border-gray-300 rounded-lg shadow-lg text-center">
        <h2 class="text-xl font-bold mb-2">E-ANTRIAN SPMB</h2>
        <p class="text-gray-600">Terima kasih atas kunjungan Anda</p>
        <hr class="my-4">
        
        <p class="text-lg">Nomor Antrian Anda:</p>
        <p class="text-6xl font-extrabold my-2 text-indigo-600">
            {{ $antrian->jenisAntrian->kode_antrian }}-{{ str_pad($antrian->nomor_antrian, 3, '0', STR_PAD_LEFT) }}
        </p>
        
        <p class="text-lg font-semibold">{{ $antrian->jenisAntrian->nama_antrian }}</p>
        <p class="text-sm text-gray-500 mt-4">
            {{ \Carbon\Carbon::parse($antrian->tanggal)->isoFormat('dddd, D MMMM YYYY') }}
        </p>
        <p class="text-sm text-gray-500">{{ $antrian->created_at->format('H:i:s') }}</p>
        
        <hr class="my-4">
        <p class="text-xs text-gray-500">Mohon menunggu untuk dipanggil.</p>
    </div>

    <!-- Instruksi Screenshot -->
    <div class="w-full max-w-sm mx-auto mt-6 bg-white p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <div class="py-1">
                <svg class="fill-current h-6 w-6 text-indigo-600 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM9 11v-2h2v2H9zm0 4v-2h2v2H9z"/></svg>
            </div>
            <div>
                <p class="font-bold text-gray-800">Penting!</p>
                <p class="text-sm text-gray-600">Silakan ambil foto atau screenshot layar ini sebagai bukti nomor antrian Anda.</p>
            </div>
        </div>
    </div>

    <div class="mt-6 flex flex-col items-center space-y-4">
        <a href="{{ route('antrian.index') }}" class="w-full max-w-sm bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 text-center">
            Kembali ke Halaman Utama
        </a>
    </div>

</x-guest-layout>