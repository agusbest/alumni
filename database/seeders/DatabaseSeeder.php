<?php

namespace Database\Seeders;

use App\Models\AlbumPhoto;
use App\Models\AlumniProfile;
use App\Models\Event;
use App\Models\GuestbookEntry;
use App\Models\LedgerEntry;
use App\Models\PlaylistTrack;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostReaction;
use App\Models\Report;
use App\Models\Setting;
use App\Models\SlamMessage;
use App\Models\User;
use App\Models\YearbookMessage;
use Illuminate\Database\Seeder;

/**
 * Data contoh FIKTIF bertema 2000-an. Semua akun alumni memakai password "password".
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put('visits', 2000 + random_int(0, 80));
        Setting::put('moderate_album', '0');

        $admin = User::create([
            'name' => 'Admin Reuni',
            'email' => 'admin@reuni2k.test',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // nama, panggilan, angkatan, kelas, kota, pekerjaan, instansi, lahir, sudah gabung?
        $raw = [
            ['Budi Santoso', 'Bubud', 2003, '3 IPA 1', 'Jakarta', 'Insinyur Sipil', 'PT Karya Bangun', '1985-11-08', 1],
            ['Sri Wahyuni', 'Wiwin', 2003, '3 IPA 1', 'Bandung', 'Guru SD', 'SD Negeri Cipaku', '1985-03-21', 1],
            ['Agus Prasetyo', 'Gupes', 2003, '3 IPS 1', 'Surabaya', 'Pemilik Bengkel', 'Bengkel Maju Jaya', '1984-12-02', 1],
            ['Rina Marlina', 'Rin2', 2003, '3 IPS 1', 'Medan', 'Apoteker', 'Apotek Sehat', '1985-07-17', 1],
            ['Hendra Kusuma', 'Hen', 2003, '3 IPA 2', 'Denpasar', 'Manajer Hotel', 'Hotel Segara', '1985-11-23', 1],
            ['Yuni Astuti', 'Yun', 2003, '3 IPA 2', 'Yogyakarta', 'Dosen', 'Universitas Contoh', '1985-05-30', 1],
            ['Tono Setiawan', 'Tonjok', 2003, '3 IPS 1', 'Palembang', 'Pedagang', '', '1984-09-12', 0],
            ['Wati Rahayu', 'Watong', 2003, '3 IPA 1', 'Semarang', 'Perawat', 'RS Harapan', '1985-01-09', 1],
            ['Eko Wahyudi', 'Ekoplo', 2004, '3 IPA 1', 'Jakarta', 'Programmer', 'Startup Nusantara', '1986-08-14', 1],
            ['Lilis Suryani', 'Lis', 2004, '3 IPS 2', 'Bekasi', 'Pebisnis Online', 'Toko Lilis', '1986-11-15', 1],
            ['Rudi Hartono', 'Rudal', 2004, '3 IPA 2', 'Balikpapan', 'Teknisi Migas', 'PT Energi Borneo', '1986-04-04', 1],
            ['Fitri Handayani', 'Fitfit', 2004, '3 IPS 2', 'Malang', 'Akuntan', 'KAP Mandiri', '1986-10-27', 1],
            ['Doni Saputra', 'Donut', 2004, '3 IPA 1', 'Makassar', 'Dokter Umum', 'Klinik Sehat Bersama', '1986-06-06', 1],
            ['Maya Puspita', 'May', 2004, '3 IPA 2', 'Kuala Lumpur', 'Desainer Grafis', 'Studio Kreasi', '1986-02-19', 1],
            ['Andi Firmansyah', 'Andong', 2004, '3 IPS 2', 'Jakarta', 'Jurnalis', 'Harian Warta', '1986-12-25', 1],
            ['Nurul Hidayah', 'Nur', 2004, '3 IPA 1', 'Surabaya', 'Notaris', 'Kantor Notaris Nurul', '1986-09-09', 1],
            ['Wawan Gunawan', 'Wewe', 2005, '3 IPA 1', 'Bandung', 'Arsitek', 'Biro Arsitek Dago', '1987-03-03', 1],
            ['Intan Permata', 'Tan2', 2005, '3 IPS 1', 'Jakarta', 'HRD Manager', 'PT Maju Bersama', '1987-11-30', 1],
            ['Bayu Nugroho', 'Bayuk', 2005, '3 IPA 2', 'Yogyakarta', 'Fotografer', 'Bayu Photography', '1987-07-22', 1],
            ['Sari Kartika', 'Sarsar', 2005, '3 IPS 1', 'Semarang', 'Pemilik Kafe', 'Kopi Kenangan Lama', '1987-05-11', 1],
            ['Dimas Anggara', 'Dims', 2005, '3 IPA 1', 'Medan', 'Pengacara', 'Kantor Hukum DA', '1987-10-05', 1],
            ['Ratna Dewanti', 'Ratnut', 2005, '3 IPA 2', 'Denpasar', 'Pemandu Wisata', 'Bali Trip', '1987-01-28', 1],
            ['Joko Susilo', 'Jokles', 2005, '3 IPS 1', 'Makassar', 'Petani Modern', '', '1987-08-18', 0],
            ['Lia Amalia', 'Lialia', 2005, '3 IPA 2', 'Jakarta', 'Content Creator', '', '1987-11-04', 0],
        ];
        $ekskul = ['Pramuka', 'PMR', 'Basket', 'Paskibra', 'Band sekolah', 'Mading', 'Karate', 'Teater'];
        $geng = ['Geng Kantin Belakang', 'Geng Bangku Pojok', 'Geng Futsal', 'Geng Anak Band', 'Geng Warnet'];
        $guru = ['Pak Darmawan (Fisika)', 'Bu Retno (Bahasa Indonesia)', 'Pak Hasan (Olahraga)', 'Bu Siti (Kimia)', 'Pak Wibowo (Sejarah)'];
        $mapel = ['Fisika', 'Bahasa Indonesia', 'Olahraga', 'Biologi', 'Sejarah', 'Ekonomi'];
        $kenangan = [
            'Bolos berjamaah ke warung Bu Narti lalu ketahuan Pak Hasan di gerbang.',
            'Kemah Pramuka yang tendanya bocor semalaman, tapi seru banget.',
            'Nitip salam lewat radio request dan kebaca di udara!',
            'Tukar-tukaran CD lagu dan nge-burn playlist pakai komputer warnet.',
            'Study tour naik bus tanpa AC, nyanyi sepanjang jalan.',
            'Pertama kali bikin akun Friendster di warnet dan pasang testimonial teman.',
        ];
        $cita = ['Jadi pilot', 'Jadi dokter', 'Jadi insinyur', 'Jadi artis', 'Jadi guru', 'Jadi pengusaha'];
        $lagu = [
            'Peterpan - Mimpi yang Sempurna',
            'Sheila On 7 - Dan',
            'Dewa 19 - Pangeran Cinta',
            'Ungu - Demi Waktu',
            'Linkin Park - In the End',
            'Avril Lavigne - Complicated',
            'Coldplay - Yellow',
            'Nidji - Hapus Aku'
        ];

        $profiles = [];
        $users = [];
        foreach ($raw as $i => [$name, $nick, $angkatan, $kelas, $city, $job, $company, $birth, $claimed]) {
            $slug = strtolower(preg_replace('/[^a-z0-9]/i', '', $nick));
            $user = null;
            if ($claimed) {
                $user = User::create([
                    'name' => $name,
                    'email' => $slug . '@contoh.test',
                    'password' => 'password',
                    'role' => 'alumni',
                    'status' => 'active',
                ]);
            }

            $p = new AlumniProfile([
                'name' => $name,
                'nickname' => $nick,
                'angkatan' => $angkatan,
                'kelas' => $kelas,
                'absen' => $claimed ? (string) (1 + ($i * 3) % 38) : null,
                'birth_date' => $birth,
                'city' => $city,
                'occupation' => $job,
                'company' => $company ?: null,
                'social' => $claimed ? '@' . $slug : null,
                'ekskul' => $ekskul[$i % count($ekskul)],
                'geng' => $geng[$i % count($geng)],
                'favorite_teacher' => $guru[$i % count($guru)],
                'favorite_subject' => $mapel[$i % count($mapel)],
                'memory' => $kenangan[$i % count($kenangan)],
                'dream_then' => $cita[$i % count($cita)],
                'dream_now' => $job,
                'favorite_song' => $lagu[$i % count($lagu)],
                'wa' => $claimed ? '0812-0000-' . (1000 + $i) : null,
                'email_public' => $claimed ? $slug . '@contoh.test' : null,
                'visibility' => [
                    'photo' => $i % 3 === 0 && $i > 8 ? 'alumni' : 'public',
                    'birth' => ($i % 2 === 0 || substr($birth, 5, 2) === '11') ? 'alumni' : 'hidden',
                    'city' => 'alumni',
                    'job' => 'alumni',
                    'social' => $i % 4 === 0 ? 'angkatan' : 'alumni',
                    'memories' => 'alumni',
                    'wa' => $i % 4 === 0 ? 'alumni' : 'hidden',
                    'email' => $i % 5 === 0 ? 'alumni' : 'hidden',
                ],
            ]);
            $p->forceFill(['user_id' => $user?->id, 'is_claimed' => (bool) $claimed])->save();

            $profiles[$i] = $p;
            $users[$i] = $user;
        }

        // Slam book & buku tahunan
        foreach (
            [
                [1, 0, 'Bubud! Jangan lupa traktir bakso di reuni nanti ya. Sukses terus, jangan sombong!'],
                [4, 0, 'Inget nggak waktu kita kabur dari upacara? Ketawa terus tiap inget.'],
                [9, 8, 'Ekoplo, tetep jadi anak komputer ya. Kapan bikinin web sekolah kita? :)']
            ] as [$from, $to, $body]
        ) {
            SlamMessage::create(['from_profile_id' => $profiles[$from]->id, 'to_profile_id' => $profiles[$to]->id, 'body' => $body]);
        }
        foreach (
            [
                [0, 'Terima kasih semuanya, 3 tahun yang tak terlupakan!'],
                [1, 'Kita pasti ketemu lagi. Jangan putus kontak ya!'],
                [8, 'Selamat jalan masa SMA. Sampai jumpa di dunia nyata!']
            ] as [$i, $body]
        ) {
            YearbookMessage::create(['profile_id' => $profiles[$i]->id, 'angkatan' => $profiles[$i]->angkatan, 'kelas' => $profiles[$i]->kelas, 'body' => $body]);
        }

        // Mading
        $pin = Post::create([
            'user_id' => $admin->id,
            'title' => 'PENDAFTARAN REUNI AKBAR DIBUKA!',
            'pinned' => true,
            'body' => 'Teman-teman alumni angkatan 2003, 2004, dan 2005, ayo daftar RSVP lewat menu Reuni & Event. Bawa foto jadul kalian ya!'
        ]);
        PostComment::create(['post_id' => $pin->id, 'user_id' => $users[8]->id, 'body' => 'Sudah daftar! Siapa lagi dari 3 IPA 1?']);
        foreach ([0, 8, 16] as $i) {
            PostReaction::create(['post_id' => $pin->id, 'user_id' => $users[$i]->id, 'emoji' => '👍']);
        }
        Post::create([
            'user_id' => $users[2]->id,
            'title' => 'Kantin Bu Narti masih ada nggak ya?',
            'body' => 'Kemarin lewat depan sekolah dan kangen banget sama gorengan dan es teh plastik. Ada yang tahu kabar Bu Narti?'
        ]);
        Post::create([
            'user_id' => $users[11]->id,
            'title' => 'Cari foto kelas 3 IPS 2 tahun 2004',
            'body' => 'Ada yang punya foto kelas 3 IPS 2? Punyaku hilang waktu pindahan rumah. Boleh dikirim ke album kenangan.'
        ]);

        // Album (gambar contoh tanpa berkas)
        foreach (
            [
                ['Study tour ke Bali, bus tanpa AC tapi hati adem', 2003, 200, 4, [0, 4, 7]],
                ['Final futsal class meeting 3 IPA 1', 2004, 30, 8, [8, 12, 15]],
                ['Persami di bumi perkemahan', 2005, 100, 16, [16, 18]],
                ['Pentas seni akhir tahun, band sekolah tampil!', 2005, 300, 19, [19, 20]]
            ] as [$cap, $year, $hue, $by, $tags]
        ) {
            $photo = AlbumPhoto::create(['user_id' => $users[$by]->id, 'caption' => $cap, 'year' => $year, 'hue' => $hue, 'status' => 'ok']);
            $photo->tags()->sync(collect($tags)->map(fn($t) => $profiles[$t]->id)->all());
        }

        // Reuni
        $e1 = Event::create([
            'title' => 'Nobar & Kumpul Santai Alumni',
            'starts_at' => now()->addDays(14)->setTime(19, 0),
            'place' => 'Kafe Kopi Kenangan Lama, Semarang',
            'description' => 'Ngopi, nostalgia, tukar foto jadul. Santai, datang saja!'
        ]);
        $e1->attendees()->sync([$users[19]->id, $users[7]->id]);
        $e2 = Event::create([
            'title' => 'REUNI AKBAR Angkatan 2003-2005',
            'starts_at' => now()->addDays(60)->setTime(9, 0),
            'place' => 'Aula SMU Negeri 3 Cibatu',
            'description' => 'Upacara bendera nostalgia, bazar kantin jadul, pentas band, dan foto bersama di depan gerbang sekolah.'
        ]);
        $e2->attendees()->sync([$users[0]->id, $users[8]->id, $users[16]->id, $users[1]->id]);

        // Guestbook, playlist, iuran
        GuestbookEntry::create(['name' => 'Wawan', 'angkatan' => 2005, 'body' => 'Mantap sekali webnya, berasa balik ke warnet jaman dulu!']);
        GuestbookEntry::create(['name' => 'Sri Wahyuni', 'angkatan' => 2003, 'body' => 'Kangen kalian semua. Sampai ketemu di reuni!']);
        GuestbookEntry::create(['name' => 'Tamu', 'angkatan' => null, 'body' => 'Salam dari adik kelas 2010 :)']);

        foreach (
            [
                ['Mimpi yang Sempurna', 'Peterpan', 2003, 0],
                ['Dan', 'Sheila On 7', 2003, 2],
                ['Pangeran Cinta', 'Dewa 19', 2005, 17],
                ['Complicated', 'Avril Lavigne', 2004, 9],
                ['In the End', 'Linkin Park', 2004, 12],
                ['Yellow', 'Coldplay', 2005, 20]
            ] as [$t, $a, $y, $by]
        ) {
            PlaylistTrack::create(['user_id' => $users[$by]->id, 'title' => $t, 'artist' => $a, 'angkatan' => $y]);
        }

        foreach (
            [
                ['in', 'Budi Santoso', 250000, 'Iuran panitia', 15],
                ['in', 'Eko Wahyudi', 250000, 'Iuran panitia', 14],
                ['in', 'Sari Kartika', 500000, 'Donasi konsumsi', 9],
                ['out', 'Percetakan Cahaya', 300000, 'Cetak spanduk', 5]
            ] as [$type, $name, $amt, $note, $ago]
        ) {
            LedgerEntry::create(['type' => $type, 'name' => $name, 'amount' => $amt, 'note' => $note, 'date' => now()->subDays($ago)->toDateString()]);
        }

        // Pendaftaran menunggu (satu klaim profil lama, satu profil baru)
        User::create([
            'name' => 'Mira Wulandari',
            'email' => 'mira@contoh.test',
            'password' => 'password',
            'role' => 'alumni',
            'status' => 'pending',
            'reg_angkatan' => 2004,
            'reg_kelas' => '3 IPS 2'
        ]);
        User::create([
            'name' => 'Tono Setiawan',
            'email' => 'tono@contoh.test',
            'password' => 'password',
            'role' => 'alumni',
            'status' => 'pending',
            'reg_angkatan' => 2003,
            'reg_kelas' => '3 IPS 1',
            'claim_profile_id' => $profiles[6]->id
        ]);

        Report::create([
            'reporter_id' => $users[3]->id,
            'type' => 'post',
            'target_id' => $pin->id,
            'label' => 'Contoh laporan: komentar kurang sopan di mading',
            'reason' => 'Dilaporkan dari mading'
        ]);
    }
}
