# E-Surat menggunakan CodeIgniter

E-Surat adalah sebuah aplikasi berbasis web untuk mengelola surat masuk dan keluar secara elektronik. Aplikasi ini dibangun menggunakan framework PHP CodeIgniter untuk memudahkan pengelolaan surat secara efisien dan hemat waktu.

## Fitur Utama

- **Manajemen Surat Masuk**: Memungkinkan pengguna untuk mengelola surat masuk dengan mudah, termasuk penyimpanan metadata, pencarian, dan penandaan status.
- **Manajemen Surat Keluar**: Pengguna dapat membuat dan mengelola surat keluar, melacak status pengiriman, dan mengatur pengiriman ulang jika diperlukan.
- **Pencarian Cepat**: Fitur pencarian canggih memungkinkan pengguna untuk dengan cepat menemukan surat berdasarkan kriteria tertentu seperti nomor surat, pengirim, atau tanggal.
- **Notifikasi**: Integrasi notifikasi untuk pengingat surat penting atau tenggat waktu.
- **Manajemen Pengguna**: Admin dapat mengelola akses pengguna, termasuk memberikan atau mencabut izin akses tertentu.

## Instalasi

1. Clone repositori ini ke dalam direktori lokal Anda:

   ```bash
   git clone https://github.com/username/E-Surat.git
   ```

2. Masuk ke direktori proyek:

   ```bash
   cd E-Surat
   ```

3. Pastikan Anda sudah menginstal PHP, MySQL, dan Composer di sistem Anda.

4. Konfigurasi basis data:

   Buatlah sebuah basis data MySQL dan ubah pengaturan koneksi basis data di file `application/config/database.php`.

5. Jalankan migrasi basis data untuk membuat skema tabel:

   ```bash
   php index.php migrate
   ```

6. Buka browser dan kunjungi `http://localhost/E-Surat` untuk mengakses aplikasi.

## Kontribusi

**E-Surat** ini masih dalam pengembangan. Saya sangat terbuka terhadap kontribusi! Jika Anda ingin berkontribusi pada proyek ini, silakan buat *pull request* dan saya akan mereviewnya.

## Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat [LICENSE](LICENSE) untuk detail lebih lanjut.
