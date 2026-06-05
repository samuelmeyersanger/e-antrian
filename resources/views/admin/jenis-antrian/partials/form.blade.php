<!-- Nama Layanan -->
<div>
    <x-input-label for="nama_antrian" :value="__('Nama Layanan (Contoh: Pendaftaran, Informasi)')" />
    <x-text-input id="nama_antrian" class="block mt-1 w-full" type="text" name="nama_antrian" :value="old('nama_antrian', $jenisAntrian->nama_antrian ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nama_antrian')" class="mt-2" />
</div>

<!-- Kode Antrian -->
<div class="mt-4">
    <x-input-label for="kode_antrian" :value="__('Kode Antrian (Contoh: A, B, C)')" />
    <x-text-input id="kode_antrian" class="block mt-1 w-full" type="text" name="kode_antrian" :value="old('kode_antrian', $jenisAntrian->kode_antrian ?? '')" required />
    <x-input-error :messages="$errors->get('kode_antrian')" class="mt-2" />
</div>

<!-- Status -->
<div class="mt-4">
    <x-input-label for="status" :value="__('Status')" />
    <select name="status" id="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
        <option value="buka" {{ (old('status', $jenisAntrian->status ?? 'buka') == 'buka') ? 'selected' : '' }}>Buka</option>
        <option value="tutup" {{ (old('status', $jenisAntrian->status ?? '') == 'tutup') ? 'selected' : '' }}>Tutup</option>
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('admin.jenis-antrian.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
        Batal
    </a>
    <x-primary-button>
        {{ isset($jenisAntrian) ? 'Update' : 'Simpan' }}
    </x-primary-button>
</div>