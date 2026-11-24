# Reuni2K.net — Aplikasi Alumni SMA (Laravel 12 + MySQL)

Aplikasi web alumni SMA bernuansa Windows 98 / 2000-an. Tampilannya **satu jendela**: menu di kiri mengganti isi
jendela, dengan tombol Kembali dan Beranda. Tahun lulus (angkatan) yang tersedia: **2000–2009** (data contoh 2003–2005).

## Kebutuhan
- PHP 8.2+ dengan ekstensi `gd`, `pdo_mysql`, `mbstring`, `fileinfo`
- Composer 2
- MySQL 8 / MariaDB 10.6+

## Instalasi
```bash
composer install
cp .env.example .env
php artisan key:generate

# Buat database (sekali saja)
mysql -u root -p -e "CREATE DATABASE reuni2k CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
# Sesuaikan DB_USERNAME / DB_PASSWORD di .env

php artisan migrate --seed      # skema + data contoh FIKTIF
php artisan serve               # http://localhost:8000
```
Foto disimpan di `storage/app/private` (disk `local`), jadi tidak perlu `storage:link`.
Pastikan folder `storage/` dan `bootstrap/cache/` bisa ditulis oleh web server.

## Akun demo (password: `password`)
| Peran | Email |
|---|---|
| Admin | `admin@reuni2k.test` |
| Alumni | `bubud@contoh.test`, `ekoplo@contoh.test`, `wewe@contoh.test`, dst. (`{panggilan}@contoh.test`) |

Seeder juga membuat 2 pendaftaran yang menunggu persetujuan (satu klaim profil lama, satu profil baru) dan
3 profil lama yang belum diklaim.

## Tes
```bash
php artisan test
```
Tes memakai SQLite in-memory dan mencakup: aturan privasi (tampilan, filter kota, peta, foto), alur daftar → verifikasi
admin → login, klaim profil lama, hak akses admin, hapus akun, RSVP, dan honeypot guestbook.

## Struktur singkat
| Bagian | Lokasi |
|---|---|
| Aturan privasi (satu sumber kebenaran) | `app/Models/AlumniProfile.php` (`canSee`, `scopeWhereCanSee`, `scopeGalleryVisible`) |
| Foto dilayani dengan cek privasi | `app/Http/Controllers/PhotoController.php`, `AlbumController@image` |
| Crop 3:4 + buang EXIF (GD) | `app/Support/ImageProcessor.php` |
| Rute | `routes/web.php` |
| Migrasi | `database/migrations/` |
| Tampilan Win98 | `resources/views/`, `public/css/win98.css`, `public/js/app.js` |
| Konfigurasi (tahun lulus, kelas, kota peta) | `config/reuni.php` |

## Peran dan privasi
- **Pengunjung**: beranda, mading (baca), reuni (info), guestbook, kuis, Snake, dan alumni yang memilih foto *Publik*.
- **Alumni**: semua menu komunitas setelah disetujui admin angkatan.
- **Admin**: verifikasi pendaftaran, laporan, moderasi foto album, tambah alumni lama, acara, iuran, pin mading.
- Tiap kolom (foto, tanggal lahir, kota, pekerjaan, sosmed, kenangan, WhatsApp, email) punya tingkat:
  Publik / Hanya Alumni / Hanya Angkatan Saya / Disembunyikan. Default kontak dan tanggal lahir: disembunyikan.
- Aturan ditegakkan **di server**: tampilan, filter/pencarian, peta, dan berkas foto. Admin tidak otomatis melihat kolom
  yang disembunyikan atau berlevel angkatan.
- Profil yang belum diklaim hanya menampilkan nama, angkatan, dan kelas.
- Hapus akun menghapus profil, foto, tulisan, dan RSVP (cascade).

## Belum ada (saran tahap berikutnya)
- Reset password dan verifikasi email (butuh konfigurasi SMTP)
- Ekspor PDF buku tahunan
- Pembayaran iuran (saat ini hanya pencatatan manual oleh admin)
- Notifikasi email/WhatsApp, impor massal alumni dari CSV
- Deploy: set `APP_ENV=production`, `APP_DEBUG=false`, pakai HTTPS, `php artisan config:cache route:cache view:cache`
