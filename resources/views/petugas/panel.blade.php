<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Panel Petugas') }} - <span class="text-indigo-600 dark:text-indigo-400">{{ $loket->nama_loket }} ({{ $loket->kode_loket }})</span>
            </h2>
            <div class="text-lg font-medium text-gray-800 dark:text-gray-200">
                Petugas: <span class="text-indigo-600 dark:text-indigo-400">{{ $petugas->nama }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Kolom Utama: Panggilan Saat Ini -->
                <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 text-center">Antrian Saat Ini</h3>
                        <div id="antrian-saat-ini" class="text-center p-8 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">-</p>
                            <p class="text-xl text-gray-600 dark:text-gray-400 mt-2">Belum ada antrian dipanggil</p>
                        </div>
                        <div id="tombol-aksi" class="mt-6 flex flex-wrap justify-center gap-4">
                            <!-- Tombol akan di-generate oleh JS -->
                        </div>
                    </div>
                </div>

                <!-- Kolom Samping: Statistik dan Daftar Tunggu -->
                <div class="space-y-6">
                    <!-- Statistik -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Statistik Hari Ini</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Total Dilayani</span>
                                <span id="total-dilayani" class="font-bold text-lg text-gray-900 dark:text-gray-100">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Sisa Antrian</span>
                                <span id="sisa-antrian" class="font-bold text-lg text-gray-900 dark:text-gray-100">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Panggil Spesifik -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Panggil Layanan Lain</h3>
                        <div class="space-y-3">
                            <div>
                                <label for="jenis-antrian-spesifik" class="sr-only">Pilih Jenis Antrian</label>
                                <select id="jenis-antrian-spesifik" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <!-- Options akan diisi oleh JS -->
                                </select>
                            </div>
                            <button onclick="handlePanggilSpesifik()" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Panggil Antrian Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Tunggu -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Daftar Tunggu</h3>
                        <div id="daftar-tunggu" class="space-y-2 h-48 overflow-y-auto">
                            <p class="text-gray-500 dark:text-gray-400 text-center">Tidak ada antrian.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const state = {
            antrianSaatIni: null,
        };

        const actionButtons = {
            panggil: `
                <button onclick="handlePanggil()" class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Panggil Berikutnya
                </button>`,
            selesaikan: `
                <button onclick="handleSelesai()" class="flex-1 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Selesai
                </button>`,
            tidakHadir: `
                <button onclick="handleTidakHadir()" class="flex-1 px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Tidak Hadir
                </button>`,
            panggilUlang: `
                <button onclick="handlePanggilUlang()" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Panggil Ulang
                </button>`,
        };

        function updateUI(data) {
            state.antrianSaatIni = data.antrian_saat_ini;

            // Update Antrian Saat Ini
            const antrianSaatIniContainer = document.getElementById('antrian-saat-ini');
            if (data.antrian_saat_ini) {
                const antrian = data.antrian_saat_ini;
                antrianSaatIniContainer.innerHTML = `
                    <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">${antrian.jenis_antrian.kode_antrian}-${String(antrian.nomor_antrian).padStart(3, '0')}</p>
                    <p class="text-xl text-gray-600 dark:text-gray-400 mt-2">${antrian.jenis_antrian.nama_antrian}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Jumlah Panggilan: ${antrian.jumlah_panggilan}</p>
                `;
            } else {
                antrianSaatIniContainer.innerHTML = `
                    <p class="text-6xl font-bold text-indigo-600 dark:text-indigo-400">-</p>
                    <p class="text-xl text-gray-600 dark:text-gray-400 mt-2">Belum ada antrian dipanggil</p>
                `;
            }

            // Update Tombol Aksi
            const tombolAksiContainer = document.getElementById('tombol-aksi');
            let buttonsHTML = '';

            if (data.antrian_saat_ini) {
                // Ada antrian aktif: tampilkan Selesai, Tidak Hadir, Panggil Ulang
                buttonsHTML = `
                    <div class="w-full flex gap-4">${actionButtons.selesaikan} ${actionButtons.tidakHadir}</div>
                    <div class="w-full mt-4">${actionButtons.panggilUlang}</div>
                `;
            } else if (data.sisa_antrian > 0) {
                // Tidak ada antrian aktif TAPI ada antrian menunggu: tampilkan Panggil Berikutnya
                buttonsHTML = actionButtons.panggil;
            } else {
                // Tidak ada antrian aktif dan tidak ada antrian menunggu
                buttonsHTML = '<p class="text-gray-500 dark:text-gray-400">Tidak ada antrian berikutnya.</p>';
            }
            tombolAksiContainer.innerHTML = buttonsHTML;

            // Update Statistik
            document.getElementById('total-dilayani').textContent = data.total_dilayani;
            document.getElementById('sisa-antrian').textContent = data.sisa_antrian;

            // Update Daftar Tunggu
            const daftarTungguContainer = document.getElementById('daftar-tunggu');
            if (data.daftar_tunggu && data.daftar_tunggu.length > 0) {
                daftarTungguContainer.innerHTML = data.daftar_tunggu.map(antrian => `
                    <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-md text-center">
                        <span class="font-bold text-gray-800 dark:text-gray-200">${antrian.jenis_antrian.kode_antrian}-${String(antrian.nomor_antrian).padStart(3, '0')}</span>
                    </div>
                `).join('');
            } else {
                daftarTungguContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">Tidak ada antrian.</p>';
            }

            // Update Dropdown Panggil Spesifik
            const jenisAntrianSpesifikSelect = document.getElementById('jenis-antrian-spesifik');
            if (data.jenis_antrian_aktif && data.jenis_antrian_aktif.length > 0) {
                const currentOptions = Array.from(jenisAntrianSpesifikSelect.options).map(o => o.value);
                const newOptions = data.jenis_antrian_aktif.map(j => String(j.id));

                // Hanya update jika ada perubahan untuk menghindari re-render yang tidak perlu
                if (JSON.stringify(currentOptions) !== JSON.stringify(newOptions)) {
                    jenisAntrianSpesifikSelect.innerHTML = data.jenis_antrian_aktif.map(jenis => `
                        <option value="${jenis.id}">${jenis.nama_antrian}</option>
                    `).join('');
                }
            } else {
                jenisAntrianSpesifikSelect.innerHTML = '<option>Tidak ada layanan aktif</option>';
            }
        }

        async function fetchState() {
            try {
                const response = await fetch('{{ route("petugas.panel.state") }}');
                const data = await response.json(); // Selalu coba parse JSON
                if (!response.ok) {
                    // Jika ada pesan error dari server (dari blok catch di controller), tampilkan
                    throw new Error(data.error || 'Gagal mengambil state dari server.');
                }
                updateUI(data);
            } catch (error) {
                console.error('Error fetching state:', error);
                // Tampilkan pesan error yang lebih spesifik di UI jika diperlukan
            }
        }

        async function postAction(routeName, body = {}) {
            try {
                const response = await fetch(routeName, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body)
                });
                const data = await response.json(); // Selalu coba parse JSON
                if (!response.ok) {
                    // Jika ada pesan error dari server, tampilkan
                    alert(data.message || 'Terjadi kesalahan saat melakukan aksi.');
                    return;
                }
                fetchState(); // Refresh state setelah aksi berhasil
            } catch (error) {
                console.error('Error posting action:', error);
                alert('Tidak dapat terhubung ke server.');
            }
        }

        function handlePanggil() {
            postAction('{{ route("petugas.panel.panggilBerikutnya") }}');
        }

        function handlePanggilUlang() {
            postAction('{{ route("petugas.panel.panggilUlang") }}');
        }

        function handleSelesai() {
            postAction('{{ route("petugas.panel.selesai") }}');
        }

        function handleTidakHadir() {
            postAction('{{ route("petugas.panel.tidakHadir") }}');
        }

        function handlePanggilSpesifik() {
            const jenisAntrianId = document.getElementById('jenis-antrian-spesifik').value;
            if (jenisAntrianId) {
                postAction('{{ route("petugas.panel.panggilSpesifik") }}', { jenis_antrian_id: jenisAntrianId });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchState();
            setInterval(fetchState, 5000); // Auto-refresh setiap 5 detik
        });
    </script>
    @endpush
</x-app-layout>