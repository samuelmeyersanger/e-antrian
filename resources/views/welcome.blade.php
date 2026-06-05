<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistem Antrian</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen flex flex-col items-center justify-center bg-gray-900 text-white">
            <div class="text-center p-6">
                @if(isset($pengaturan['logo_sekolah']) && $pengaturan['logo_sekolah'])
                    <img src="{{ asset('storage/' . $pengaturan['logo_sekolah']) }}" alt="Logo Sekolah" class="mx-auto h-20 w-auto mb-6">
                @endif
                <h1 class="text-5xl md:text-7xl font-bold mb-4 text-indigo-400">
                    Sistem Antrian SPMB
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8">
                    Selamat Datang di Sistem Antrian Penerimaan Siswa Baru <br> SMP NEGERI 4 CIBITUNG T.A 2026/2027
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('antrian.index') }}" class="w-full sm:w-auto inline-block px-8 py-4 text-lg font-semibold text-white bg-green-600 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition-transform transform hover:scale-105">
                        Ambil Antrian
                    </a>
                    <a href="{{ route('monitor.index') }}" class="w-full sm:w-auto inline-block px-8 py-4 text-lg font-semibold text-gray-900 bg-yellow-400 rounded-lg shadow-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 focus:ring-offset-gray-900 transition-transform transform hover:scale-105">
                        Lihat Monitor Antrian
                    </a>
                </div>
            </div>
            <div class="absolute bottom-4 text-center w-full text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} SPMB SMPN 4 CIBITUNG. Created by Samuel Meyer Sanger, MTCRE.</p>
            </div>
        </div>
    </body>
</html>