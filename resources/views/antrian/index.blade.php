<x-guest-layout>
    <div class="mb-4 text-center">
        <h1 class="text-2xl font-bold text-white">AMBIL NOMOR ANTRIAN</h1>
        <p class="text-white">Silakan pilih jenis layanan Anda</p>
    </div>

    <form method="POST" action="{{ route('antrian.store') }}">
        @csrf

        <!-- Pilihan Jenis Antrian -->
        <div>
            <x-input-label for="jenis_antrian_id" :value="__('Jenis Layanan')" />
            <select id="jenis_antrian_id" name="jenis_antrian_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">-- Pilih Layanan --</option>
                @foreach($jenisAntrian as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama_antrian }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jenis_antrian_id')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-6">
            <x-primary-button class="w-full text-center">
                <span class="w-full">{{ __('Dapatkan Nomor Antrian') }}</span>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
