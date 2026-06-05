<!-- Nama -->
<div>
    <x-input-label for="nama" :value="__('Nama')" />
    <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama', $petugas->nama ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
</div>

<!-- Email -->
<div class="mt-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $petugas->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<!-- Loket -->
<div class="mt-4">
    <x-input-label for="loket_id" :value="__('Tugaskan ke Loket')" />
    <select id="loket_id" name="loket_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
        <option value="">-- Tidak Ditugaskan --</option>
        @foreach($loket as $loket)
            <option value="{{ $loket->id }}" @selected(old('loket_id', $petugas->loket_id ?? '') == $loket->id)>
                {{ $loket->nama_loket }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('loket_id')" class="mt-2" />
</div>

<!-- Password -->
<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />
    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" :required="!isset($petugas)" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
    @if(isset($petugas))
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
    @endif
</div>

<!-- Konfirmasi Password -->
<div class="mt-4">
    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" :required="!isset($petugas)" />
    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
</div>


<div class="flex items-center justify-end mt-6">
    <a href="{{ route('admin.petugas.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">
        Batal
    </a>
    <x-primary-button>
        {{ isset($petugas) ? 'Update' : 'Simpan' }}
    </x-primary-button>
</div>