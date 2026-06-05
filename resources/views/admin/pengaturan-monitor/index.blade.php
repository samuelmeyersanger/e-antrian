<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Monitor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Menampilkan pesan sukses atau error -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.pengaturan-monitor.update', $pengaturan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Warna Latar -->
                            <div>
                                <label for="warna_latar" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Warna Latar</label>
                                <input type="color" id="warna_latar" name="warna_latar" value="{{ old('warna_latar', $pengaturan->warna_latar) }}" class="mt-1 block w-full">
                            </div>

                            <!-- Warna Teks -->
                            <div>
                                <label for="warna_teks" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Warna Teks</label>
                                <input type="color" id="warna_teks" name="warna_teks" value="{{ old('warna_teks', $pengaturan->warna_teks) }}" class="mt-1 block w-full">
                            </div>

                            <!-- Font Teks -->
                            <div>
                                <label for="font_teks" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Font Teks</label>
                                <input type="text" id="font_teks" name="font_teks" value="{{ old('font_teks', $pengaturan->font_teks) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            </div>

                            <!-- Ukuran Teks -->
                            <div>
                                <label for="ukuran_teks" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Ukuran Teks (px)</label>
                                <input type="number" id="ukuran_teks" name="ukuran_teks" value="{{ old('ukuran_teks', $pengaturan->ukuran_teks) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            </div>

                            <!-- Logo -->
                            <div class="md:col-span-2">
                                <label for="logo" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Logo</label>
                                <input type="file" id="logo" name="logo" class="mt-1 block w-full">
                                @if ($pengaturan->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo" class="h-20">
                                    </div>
                                @endif
                            </div>

                            <!-- Video -->
                            <div class="md:col-span-2">
                                <label for="video" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Video Latar</label>
                                <input type="file" id="video" name="video" class="mt-1 block w-full">
                                @if ($pengaturan->video)
                                    <div class="mt-2">
                                        <video width="320" height="240" controls>
                                          <source src="{{ asset('storage/' . $pengaturan->video) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @endif
                            </div>

                            <!-- Running Text -->
                            <div class="md:col-span-2">
                                <label for="running_text" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Running Text</label>
                                <textarea id="running_text" name="running_text" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('running_text', $pengaturan->running_text) }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
