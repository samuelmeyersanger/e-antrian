<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel Panggilan Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Pemilihan Loket -->
                    <div class="mb-6">
                        <label for="loket_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Pilih Loket untuk Memanggil</label>
                        <select id="loket_id" name="loket_id" class="block w-full mt-1 rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Pilih Loket --</option>
                            @foreach ($lokets as $loket)
                                <option value="{{ $loket->id }}">{{ $loket->nama_loket }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Panel Panggilan (Awalnya Tersembunyi) -->
                    <div id="panel-panggilan" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Kolom Antrian Saat Ini -->
                            <div class="bg-gray-100 dark:bg-gray-700 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Antrian Saat Ini</h3>
                                <div id="current-ticket" class="text-center">
                                    <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">-</p>
                                    <p class="text-gray-500 dark:text-gray-400 mt-2">Belum ada antrian dipanggil</p>
                                </div>
                            </div>

                            <!-- Kolom Info dan Aksi -->
                            <div class="bg-gray-100 dark:bg-gray-700 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold mb-4">Informasi Loket</h3>
                                <div id="loket-info">
                                    <p>Sisa Antrian: <span id="sisa-antrian" class="font-bold">0</span></p>
                                    <p>Total Dilayani: <span id="total-dilayani" class="font-bold">0</span></p>
                                </div>
                                <!-- Tombol Aksi Panggilan -->
                                <div id="grup-tombol-panggil">
                                    <div class="mt-6 space-y-4">
                                        <x-primary-button id="panggil-berikutnya" class="w-full justify-center !py-3 !text-lg">
                                            Panggil Berikutnya (Umum)
                                        </x-primary-button>
                                        <x-secondary-button id="panggil-terlewat" class="w-full justify-center !py-2">
                                            Panggil Antrian Terlewat
                                        </x-secondary-button>
                                    </div>

                                    <!-- Panggilan Spesifik -->
                                    <div class="mt-6 border-t dark:border-gray-600 pt-4">
                                        <label for="jenis_antrian_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Panggil dari Layanan Spesifik</label>
                                        <select id="jenis_antrian_id" class="block w-full mt-1 rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <!-- Opsi akan diisi oleh JS -->
                                        </select>
                                        <x-primary-button id="panggil-spesifik" class="w-full justify-center mt-2 !py-2">
                                            Panggil Pilihan
                                        </x-primary-button>
                                    </div>
                                </div>

                                <!-- Tombol Aksi Selesai -->
                                <div id="grup-tombol-selesai" class="hidden mt-6 space-y-4">
                                    <x-secondary-button id="panggil-ulang" class="w-full justify-center !py-3">
                                        Panggil Ulang
                                    </x-secondary-button>
                                    <x-primary-button id="selesai" class="w-full justify-center !py-3 !text-lg bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:ring-green-500">
                                        Selesai
                                    </x-primary-button>
                                    <x-danger-button id="tidak-hadir" class="w-full justify-center !py-3">
                                        Tidak Hadir
                                    </x-danger-button>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div id="panel-loading" class="text-center p-6 hidden">
                        <p>Memuat data panel...</p>
                    </div>
                    <div id="panel-error" class="text-center p-6 hidden">
                        <p class="text-red-500">Gagal memuat data. Silakan coba lagi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loketSelect = document.getElementById('loket_id');
            const panelPanggilan = document.getElementById('panel-panggilan');
            const panelLoading = document.getElementById('panel-loading');
            const panelError = document.getElementById('panel-error');

            const currentTicketEl = document.getElementById('current-ticket');
            const sisaAntrianEl = document.getElementById('sisa-antrian');
            const totalDilayaniEl = document.getElementById('total-dilayani');
            const jenisAntrianSelect = document.getElementById('jenis_antrian_id');
            
            const panggilBerikutnyaBtn = document.getElementById('panggil-berikutnya');
            const panggilUlangBtn = document.getElementById('panggil-ulang');
            const panggilSpesifikBtn = document.getElementById('panggil-spesifik');
            const panggilTerlewatBtn = document.getElementById('panggil-terlewat');
            const selesaiBtn = document.getElementById('selesai');
            const tidakHadirBtn = document.getElementById('tidak-hadir');

            const grupTombolPanggil = document.getElementById('grup-tombol-panggil');
            const grupTombolSelesai = document.getElementById('grup-tombol-selesai');

            let selectedLoketId = null;

            loketSelect.addEventListener('change', function() {
                selectedLoketId = this.value;
                if (selectedLoketId) {
                    panelPanggilan.classList.add('hidden');
                    panelLoading.classList.remove('hidden');
                    panelError.classList.add('hidden');
                    fetchState();
                } else {
                    panelPanggilan.classList.add('hidden');
                    panelLoading.classList.add('hidden');
                    panelError.classList.add('hidden');
                }
            });

            async function fetchState() {
                if (!selectedLoketId) return;

                try {
                    const response = await fetch(`/petugas/panel/state?loket_id=${selectedLoketId}`);
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Network response was not ok.');
                    }
                    
                    updateUI(data);

                    panelLoading.classList.add('hidden');
                    panelPanggilan.classList.remove('hidden');

                } catch (error) {
                    console.error('Error fetching state:', error);
                    panelLoading.classList.add('hidden');
                    panelError.classList.remove('hidden');
                }
            }

            function updateUI(data) {
                if (data.antrian_saat_ini) {
                    currentTicketEl.innerHTML = `
                        <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">${data.antrian_saat_ini.jenis_antrian.kode_antrian}-${String(data.antrian_saat_ini.nomor_antrian).padStart(3, '0')}</p>
                        <p class="text-xl text-gray-500 dark:text-gray-400 mt-2">${data.antrian_saat_ini.jenis_antrian.nama_antrian}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Jumlah Panggilan: ${data.antrian_saat_ini.jumlah_panggilan}</p>
                    `;
                    grupTombolPanggil.classList.add('hidden');
                    grupTombolSelesai.classList.remove('hidden');
                } else {
                    currentTicketEl.innerHTML = `
                        <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">-</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Belum ada antrian dipanggil</p>
                    `;
                    grupTombolPanggil.classList.remove('hidden');
                    grupTombolSelesai.classList.add('hidden');
                }
                sisaAntrianEl.textContent = data.sisa_antrian;
                totalDilayaniEl.textContent = data.total_dilayani;

                // Update dropdown jenis antrian
                jenisAntrianSelect.innerHTML = '<option value="">-- Pilih Jenis Layanan --</option>';
                if (data.jenis_antrian_aktif) {
                    data.jenis_antrian_aktif.forEach(jenis => {
                        const option = document.createElement('option');
                        option.value = jenis.id;
                        option.textContent = `${jenis.nama_antrian} (${jenis.kode_antrian})`;
                        jenisAntrianSelect.appendChild(option);
                    });
                }
            }

            async function callAction(action, body = {}) {
                if (!selectedLoketId) return;

                // Selalu tambahkan loket_id ke body
                body.loket_id = selectedLoketId;

                try {
                    const response = await fetch(`/petugas/panel/${action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(body)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Terjadi kesalahan pada server.');
                    }
                    
                    updateUI(data);

                } catch (error) {
                    console.error(`Error on ${action}:`, error);
                    alert(error.message || 'Gagal melakukan aksi. Periksa konsol untuk detail.');
                }
            }

            panggilBerikutnyaBtn.addEventListener('click', () => callAction('panggil-berikutnya'));
            panggilUlangBtn.addEventListener('click', () => callAction('panggil-ulang'));
            panggilTerlewatBtn.addEventListener('click', () => callAction('panggil-terlewat'));
            selesaiBtn.addEventListener('click', () => callAction('selesai'));
            tidakHadirBtn.addEventListener('click', () => callAction('tidak-hadir'));

            panggilSpesifikBtn.addEventListener('click', () => {
                const jenisAntrianId = jenisAntrianSelect.value;
                if (!jenisAntrianId) {
                    alert('Silakan pilih jenis layanan terlebih dahulu.');
                    return;
                }
                callAction('panggil-spesifik', { jenis_antrian_id: jenisAntrianId });
            });

            // Initial fetch if a loket is pre-selected (e.g. from old value)
            if (loketSelect.value) {
                loketSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
</x-app-layout>