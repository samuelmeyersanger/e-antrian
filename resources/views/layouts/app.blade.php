<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white dark:bg-gray-800 shadow-inner">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} SPMB SMPN 4 CIBITUNG. Created by Samuel Meyer Sanger, MTCRE.
                    </p>
                </div>
            </footer>
        </div>

        <!-- Custom Alert Modal -->
        <div id="custom-alert-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center z-50 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                    <h3 id="custom-alert-title" class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pemberitahuan</h3>
                    <button onclick="hideCustomAlert()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="mt-4">
                    <p id="custom-alert-message" class="text-gray-700 dark:text-gray-300">Isi pesan di sini.</p>
                </div>
                <div class="mt-6 text-right">
                    <button onclick="hideCustomAlert()" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <script>
            const customAlertModal = document.getElementById('custom-alert-modal');
            const customAlertTitle = document.getElementById('custom-alert-title');
            const customAlertMessage = document.getElementById('custom-alert-message');

            function showCustomAlert(message, title = 'Pemberitahuan') {
                customAlertTitle.textContent = title;
                customAlertMessage.textContent = message;
                customAlertModal.classList.remove('hidden');
            }

            function hideCustomAlert() {
                customAlertModal.classList.add('hidden');
            }
        </script>

        @stack('scripts')
    </body>
</html>