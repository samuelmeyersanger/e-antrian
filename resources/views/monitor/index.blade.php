<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Monitor Antrian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Animasi untuk item yang baru dipanggil */
        .new-call {
            animation: flash-animation 1.5s 3; /* Flash 3 kali */
        }
        @keyframes flash-animation {
            0% { background-color: #fefcbf; } /* yellow-100 */
            50% { background-color: #fde047; } /* yellow-400 */
            100% { background-color: #fefcbf; }
        }
        .marquee {
            white-space: nowrap;
            overflow: hidden;
            box-sizing: border-box;
        }
        .marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 15s linear infinite;
        }
        @keyframes marquee {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-100%, 0); }
        }
    </style>
</head>
<body class="bg-gray-900 text-white font-sans overflow-hidden">

    <div class="flex flex-col h-screen">
        <!-- Header -->
        <header class="relative text-center py-3 bg-gray-800 shadow-lg">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold">SISTEM ANTRIAN</h1>
                <p class="text-lg md:text-xl text-gray-300">SPMB T.A 2026/2027</p>
            </div>
            <div id="logo-container" class="absolute right-6 top-0 bottom-0 flex items-center">
                <!-- Logo akan dimasukkan di sini oleh JS -->
            </div>
        </header>

        <!-- Konten Utama -->
        <main class="flex-grow grid grid-cols-1 lg:grid-cols-4">
            
            <!-- Kolom Kiri: Video/Gambar (Lebih Besar) -->
            <div id="media-container" class="lg:col-span-3 bg-gray-800 flex items-center justify-center">
                <!-- Konten media akan diisi oleh JS -->
                <div class="w-full h-full bg-black flex items-center justify-center">
                    <p class="text-gray-500">Memuat media...</p>
                </div>
            </div>

            <!-- Kolom Kanan: Daftar Panggilan -->
            <aside class="lg:col-span-1 bg-gray-800 flex flex-col">
                <div class="bg-gray-700 p-3 shadow-md">
                    <h2 class="text-2xl font-semibold text-center text-yellow-300">PANGGILAN</h2>
                </div>
                <div id="panggilan-container" class="flex-grow p-4 space-y-4 overflow-y-auto">
                    <!-- Data panggilan akan dimasukkan oleh JavaScript di sini -->
                    <p class="text-center text-gray-400">Menunggu panggilan...</p>
                </div>
            </aside>
        </main>

        <!-- Footer: Riwayat dan Running Text -->
        <footer class="bg-gray-800 shadow-lg">
            <!-- Riwayat Panggilan -->
            <div class="bg-gray-700 p-2">
                <h3 class="text-center text-lg font-semibold text-gray-200">RIWAYAT PANGGILAN</h3>
            </div>
            <div id="riwayat-panggilan" class="p-3 flex justify-center items-center space-x-4 overflow-hidden">
                <!-- Riwayat akan diisi oleh JS -->
                <p class="text-gray-500">Tidak ada riwayat.</p>
            </div>
            <!-- Running Text -->
            <div class="bg-black p-2">
                <div class="marquee">
                    <span id="running-text" class="text-lg text-yellow-300">
                        Memuat informasi...
                    </span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Speech Synthesizer -->
    <script src="{{ asset('js/speech-synthesizer.js') }}"></script>

    <script>
        let lastCalledTime = null;

        function createPanggilanCard(panggilan, isBesar = false) {
            const nomorLengkap = panggilan ? panggilan.nomor_lengkap : '-';
            const namaLoket = panggilan ? panggilan.nama_loket : 'Tunggu';

            const textSize = isBesar ? 'text-5xl' : 'text-3xl';
            const loketTextSize = isBesar ? 'text-2xl' : 'text-xl';
            const bgColor = isBesar ? 'bg-yellow-400 text-gray-900' : 'bg-gray-700';
            const cardClass = `p-4 rounded-lg shadow-md text-center transform transition-transform duration-500 ${bgColor}`;

            return `
                <div class="${cardClass}" data-nomor="${nomorLengkap}">
                    <p class="${textSize} font-bold">${nomorLengkap}</p>
                    <p class="${loketTextSize} font-semibold">${namaLoket}</p>
                </div>
            `;
        }

        function createRiwayatCard(panggilan) {
            return `
                <div class="bg-gray-600 p-2 rounded-md text-center">
                    <p class="font-bold text-gray-200">${panggilan.nomor_lengkap}</p>
                    <p class="text-sm text-gray-300">${panggilan.nama_loket}</p>
                </div>
            `;
        }

        function updateUI(data) {
            const panggilanContainer = document.getElementById('panggilan-container');
            const mediaContainer = document.getElementById('media-container');
            const runningText = document.getElementById('running-text');
            const logoContainer = document.getElementById('logo-container');
            const riwayatContainer = document.getElementById('riwayat-panggilan'); // Definisi yang hilang

            // Update Running Text, Logo, dan Media
            if (data.pengaturan) {
                if (data.pengaturan.running_text) {
                    runningText.textContent = data.pengaturan.running_text;
                }
                if (data.pengaturan.logo_url) {
                    logoContainer.innerHTML = `<img src="${data.pengaturan.logo_url}" alt="Logo" class="h-16">`;
                } else {
                    logoContainer.innerHTML = ''; // Kosongkan jika tidak ada logo
                }

                // Update Media (Video/Gambar)
                if (data.pengaturan.video_url) {
                    // Cek apakah URL adalah video YouTube
                    if (data.pengaturan.video_url.includes('youtube.com') || data.pengaturan.video_url.includes('youtu.be')) {
                        const videoId = data.pengaturan.video_url.split('v=')[1] || data.pengaturan.video_url.split('/').pop();
                        const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}&controls=0&showinfo=0&rel=0`;
                        mediaContainer.innerHTML = `<iframe class="w-full h-full" src="${embedUrl}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
                    } else {
                        // Asumsikan sebagai URL video atau gambar biasa
                        const isVideo = /\.(mp4|mov|ogg|qt)$/i.test(data.pengaturan.video_url);
                        if (isVideo) {
                             mediaContainer.innerHTML = `<video src="${data.pengaturan.video_url}" class="w-full h-full object-cover" autoplay muted loop></video>`;
                        } else {
                             mediaContainer.innerHTML = `<img src="${data.pengaturan.video_url}" class="w-full h-full object-cover" alt="Media Promosi">`;
                        }
                    }
                } else {
                     mediaContainer.innerHTML = `
                        <div class="w-full h-full bg-black flex items-center justify-center">
                            <p class="text-gray-500">Tidak ada media dikonfigurasi.</p>
                        </div>`;
                }
            }

            panggilanContainer.innerHTML = ''; // Kosongkan kontainer

            const antrianDipanggil = data.antrian_dipanggil || {};

            // Tampilkan antrian yang sedang dipanggil
            let hasPanggilan = false;
            for (const kodeLoket in antrianDipanggil) {
                const panggilan = antrianDipanggil[kodeLoket];
                if (panggilan) {
                    hasPanggilan = true;
                    panggilanContainer.innerHTML += createPanggilanCard(panggilan, true);
                }
            }

            if (!hasPanggilan) {
                panggilanContainer.innerHTML = '<p class="text-center text-gray-400">Menunggu panggilan...</p>';
            }

            // Tampilkan riwayat panggilan
            const riwayatPanggilan = data.riwayat_panggilan || [];
            if (riwayatPanggilan.length > 0) {
                riwayatContainer.innerHTML = riwayatPanggilan.map(createRiwayatCard).join('');
            } else {
                riwayatContainer.innerHTML = '<p class="text-gray-500">Tidak ada riwayat.</p>';
            }

            // Logika untuk suara dan animasi highlight
            const panggilanTerbaru = data.panggilan_terbaru;
            console.log("Menerima panggilan terbaru:", panggilanTerbaru); // DEBUG

            if (panggilanTerbaru && (!lastCalledTime || new Date(panggilanTerbaru.waktu_panggilan) > new Date(lastCalledTime))) {
                console.log("Memutar suara untuk:", panggilanTerbaru.nomor_lengkap); // DEBUG
                lastCalledTime = panggilanTerbaru.waktu_panggilan;

                // Gunakan Speech Synthesizer untuk memanggil
                const textToSpeak = window.speechSynthesizer.formatPanggilan(panggilanTerbaru);
                if (textToSpeak) {
                    window.speechSynthesizer.speak(textToSpeak);
                }

                // Highlight kartu yang sesuai
                setTimeout(() => {
                    const cardToHighlight = document.querySelector(`[data-nomor="${panggilanTerbaru.nomor_lengkap}"]`);
                    if (cardToHighlight) {
                        cardToHighlight.classList.add('new-call');
                        // Hapus class setelah animasi selesai agar bisa berkedip lagi nanti
                        setTimeout(() => cardToHighlight.classList.remove('new-call'), 4500);
                    }
                }, 100); // Beri sedikit waktu agar elemen ada di DOM
            }
        }

        async function fetchState() {
            try {
                const response = await fetch("{{ route('monitor.data') }}");
                if (!response.ok) {
                    // Jika response tidak OK (misal: error 500), coba baca body sebagai JSON
                    const errorData = await response.json();
                    const errorMessage = `Gagal memuat data: ${errorData.message || response.statusText} (File: ${errorData.file || 'N/A'}, Line: ${errorData.line || 'N/A'})`;
                    throw new Error(errorMessage);
                }
                const data = await response.json();
                updateUI(data);
                // Set pesan error ke string kosong jika berhasil
                document.getElementById('panggilan-container').dataset.error = '';
            } catch (error) {
                console.error('Error fetching state:', error);
                const errorContainer = document.getElementById('panggilan-container');
                // Tampilkan pesan error di UI dan simpan di data-attribute
                const errorMessage = error.message || 'Gagal memuat data. Memeriksa kembali...';
                if (errorContainer.dataset.error !== errorMessage) {
                    errorContainer.innerHTML = `<div class="text-red-400 text-center p-4">${errorMessage}</div>`;
                    errorContainer.dataset.error = errorMessage;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchState();
            setInterval(fetchState, 5000); // Auto-refresh setiap 5 detik
        });
    </script>
</body>
</html>