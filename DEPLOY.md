# Deploy gratis satu platform: Render (web + PostgreSQL)

Kode mendukung MySQL (lokal/Railway/cPanel) dan PostgreSQL (Render). Pilih lewat `DB_CONNECTION`.

## PENTING (Render free)
1. Disk Render free sementara: foto upload (disimpan di `storage/app/private`) HILANG saat redeploy/restart/spin-down.
   Data teks aman di database; foto kembali ke avatar default.
2. PostgreSQL free Render punya masa berlaku terbatas (docs Render: kedaluwarsa 30 hari setelah dibuat). Backup/upgrade sebelum itu.
3. Service tidur setelah 15 menit idle; bangun ±1 menit.
Untuk produksi sungguhan: Render berbayar (+Disk, DB berbayar), Railway, atau VPS.

## Langkah
1. Push proyek ke GitHub.
2. Render -> New -> **Blueprint** -> pilih repo. `render.yaml` membuat web service (Docker) + database `reuni2k-db`;
   `DB_URL` terisi otomatis dari database.
3. Isi 2 env yang diminta: `APP_KEY` (jalankan `php artisan key:generate --show` di komputer Anda) dan `APP_URL`
   (https://nama-app.onrender.com, tahu setelah service dibuat; isi lalu redeploy).
4. Opsional: `RUN_SEED=1` sekali untuk data demo, lalu hapus variabel itu.
5. Migrasi otomatis saat start. Cek `/up`.

## Lokal dengan PostgreSQL
`DB_CONNECTION=pgsql`, `DB_HOST/PORT/DATABASE/USERNAME/PASSWORD` atau `DB_URL=postgres://...`.

## Railway (berbayar)
Repo yang sama + service MySQL/Postgres, isi DB_*, pasang Volume di `storage/app/private`.

## Catatan
- Akun demo (password `password`) hanya uji coba; ganti sebelum dipakai alumni sungguhan.
- Session memakai file: login hilang saat restart. Opsional SESSION_DRIVER=database.
