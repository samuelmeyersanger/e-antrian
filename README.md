# E-Antrian

Aplikasi manajemen antrian digital untuk meningkatkan efisiensi layanan. Dibangun menggunakan Laravel Framework dengan lingkungan pengembangan berbasis Docker (Laravel Sail).

## Prasyarat

Pastikan sistem Anda telah memenuhi persyaratan berikut sebelum memulai:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- WSL2 (khusus untuk pengguna Windows)
- Git

## Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda.

1.  **Clone Repositori**
    ```bash
    git clone https://github.com/username/e-antrian.git
    cd e-antrian
    ```

2.  **Buat File Konfigurasi Lingkungan**
    Salin file `.env.example` untuk membuat file konfigurasi `.env` Anda sendiri.
    ```bash
    cp .env.example .env
    ```
    *File `.env` berisi semua konfigurasi sensitif dan spesifik untuk mesin Anda. File ini tidak akan pernah di-commit ke Git.*

3.  **Jalankan Container Sail**
    Perintah ini akan mengunduh *image* Docker yang diperlukan dan menjalankan semua layanan (aplikasi, database, dll.) di latar belakang.
    ```bash
    ./vendor/bin/sail up -d
    ```
    *Catatan: Proses ini mungkin memakan waktu cukup lama saat pertama kali dijalankan.*

4.  **Instal Dependensi Composer**
    Instal semua pustaka PHP yang dibutuhkan oleh proyek.
    ```bash
    ./vendor/bin/sail composer install
    ```

5.  **Generate Kunci Aplikasi**
    Buat kunci enkripsi unik untuk aplikasi Anda.
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Jalankan Migrasi Database**
    Buat semua tabel yang diperlukan di dalam database.
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

7.  **(Opsional) Instal Dependensi NPM & Compile Aset**
    Jika proyek ini memiliki aset frontend (JavaScript/CSS) yang perlu di-compile.
    ```bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev
    ```

8.  **Selesai!**
    Aplikasi Anda sekarang seharusnya sudah berjalan dan dapat diakses melalui **[http://localhost](http://localhost)**.

## Penggunaan Laravel Sail

Berikut adalah beberapa perintah umum Laravel Sail untuk mengelola lingkungan pengembangan Anda:

- **Menjalankan lingkungan (di latar belakang):**
  ```bash
  ./vendor/bin/sail up -d
  ```

- **Menghentikan lingkungan:**
  ```bash
  ./vendor/bin/sail down
  ```

- **Menjalankan perintah Artisan:**
  ```bash
  ./vendor/bin/sail artisan <nama-perintah>
  ```
  Contoh: `./vendor/bin/sail artisan make:controller UserController`

- **Menjalankan perintah Composer:**
  ```bash
  ./vendor/bin/sail composer <nama-perintah>
  ```

- **Membuka shell di dalam container aplikasi:**
  ```bash
  ./vendor/bin/sail shell
  ```

## Menjalankan Tes

Untuk menjalankan *automated tests* yang ada di proyek, gunakan perintah berikut:
```bash
./vendor/bin/sail artisan test
```


docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs