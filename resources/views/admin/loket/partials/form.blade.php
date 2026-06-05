<!-- Nama Loket -->
<div>
    <x-input-label for="nama_loket" :value="__('Nama Loket')" />
    <x-text-input id="nama_loket" class="block mt-1 w-full" type="text" name="nama_loket" :value="old('nama_loket', $loket->nama_loket ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nama_loket')" class="mt-2" />
</div>

<!-- Kode Loket -->
<div class="mt-4">
    <x-input-label for="kode_loket" :value="__('Kode Loket (Contoh: A, B, C)')" />
    <x-text-input id="kode_loket" class="block mt-1 w-full" type="text" name="kode_loket" :value="old('kode_loket', $loket->kode_loket ?? '')" required />
    <x-input-error :messages="$errors->get('kode_loket')" class="mt-2" />
</div>

<!-- Jenis Antrian -->
<div class="mt-4">
    <x-input-label for="jenis_antrian_id" :value="__('Jenis Layanan yang Dilayani')" />
    <select name="jenis_antrian_id" id="jenis_antrian_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
        <option value="">-- Pilih Jenis Layanan --</option>
        @foreach($jenisAntrian as $jenis)
            <option value="{{ $jenis->id }}" {{ (old('jenis_antrian_id', $loket->jenis_antrian_id ?? '') == $jenis->id) ? 'selected' : '' }}>
                {{ $jenis->nama_antrian }} ({{ $jenis->kode_antrian }})
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('jenis_antrian_id')" class="mt-2" />
</div>

<!-- Status -->
<div class="mt-4">
    <x-input-label for="status" :value="__('Status Loket')" />
    <select name="status" id="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
        <option value="buka" {{ (old('status', $loket->status ?? 'buka') == 'buka') ? 'selected' : '' }}>Buka</option>
        <option value="tutup" {{ (old('status', $loket->status ?? '') == 'tutup') ? 'selected' : '' }}>Tutup</option>
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('admin.loket.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
        Batal
    </a>
    <x-primary-button>
        {{ isset($loket) ? 'Update' : 'Simpan' }}
    </x-primary-button>
</div>