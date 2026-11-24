<?php

namespace App\Support;

/**
 * Membuat avatar siluet SVG (bergaya foto pas jadul) untuk profil tanpa foto,
 * profil yang belum bergabung, dan foto yang disembunyikan pemiliknya.
 */
class Avatar
{
    public static function svg(string|int $seed, string $kind, ?int $angkatan = null): string
    {
        $h = crc32($seed.'|'.$kind);
        $gray = in_array($kind, ['lock', 'anon'], true);

        $bgs = $kind === 'then'
            ? ['#d9c7a3', '#c9b48a', '#e0cfae', '#bfae8f']
            : ['#9ec5d6', '#b5d3a8', '#d6b3c9', '#c9c9e6'];
        $skins = ['#e8b98f', '#c98d62', '#f1c9a5', '#a8714a'];
        $hairs = ['#1b1b1b', '#3b2415', '#5a3a1e', '#111111'];
        $shirts = ['#2f5d8a', '#8a2f4a', '#2f8a5d', '#555555'];

        $bg = $gray ? '#b8b8b8' : $bgs[$h % 4];
        $skin = $gray ? '#8c8c8c' : $skins[($h >> 3) % 4];
        $hair = $gray ? '#6f6f6f' : $hairs[($h >> 5) % 4];
        $shirt = $gray ? '#7a7a7a' : ($kind === 'then' ? '#f4f4f4' : $shirts[($h >> 7) % 4]);
        $style = ($h >> 2) % 3;

        $hairPath = match ($style) {
            0 => '<path d="M30 52 Q45 20 60 52 Q60 36 45 34 Q30 36 30 52Z" fill="'.$hair.'"/>',
            1 => '<path d="M28 58 Q26 24 45 24 Q64 24 62 58 Q58 38 45 38 Q32 38 28 58Z" fill="'.$hair.'"/>',
            default => '<ellipse cx="45" cy="34" rx="18" ry="10" fill="'.$hair.'"/>',
        };

        $extra = '';
        if ($kind === 'then' && $angkatan) {
            $extra = '<text x="84" y="115" font-size="9" font-family="monospace" fill="#c2561c" text-anchor="end">\''.substr((string) $angkatan, -2).'</text>';
        }
        $mark = match ($kind) {
            'lock' => '<text x="45" y="64" font-size="22" text-anchor="middle">&#128274;</text>',
            'anon' => '<text x="45" y="66" font-size="20" font-family="monospace" text-anchor="middle" fill="#fff">?</text>',
            default => '',
        };

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 120">'
            .'<rect width="90" height="120" fill="'.$bg.'"/>'
            .'<path d="M8 120 Q10 82 45 80 Q80 82 82 120Z" fill="'.$shirt.'"/>'
            .'<rect x="38" y="66" width="14" height="16" fill="'.$skin.'"/>'
            .'<ellipse cx="45" cy="50" rx="17" ry="20" fill="'.$skin.'"/>'
            .($gray ? '' : $hairPath).$mark.$extra.'</svg>';
    }

    /** Gambar contoh untuk foto album seed. */
    public static function scene(string $title, int $hue): string
    {
        $t = htmlspecialchars($title, ENT_QUOTES | ENT_XML1);

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 120"><defs><linearGradient id="g" x1="0" y1="0" x2="0" y2="1">'
            .'<stop offset="0" stop-color="hsl('.$hue.',60%,70%)"/><stop offset="1" stop-color="hsl('.($hue + 30).',70%,88%)"/></linearGradient></defs>'
            .'<rect width="160" height="120" fill="url(#g)"/><circle cx="125" cy="28" r="12" fill="#ffe27a"/>'
            .'<path d="M0 95 Q30 60 60 90 T120 85 T160 92 V120H0Z" fill="hsl('.($hue + 120).',35%,38%)"/>'
            .'<g fill="#222"><circle cx="55" cy="84" r="5"/><rect x="50" y="89" width="10" height="18"/><circle cx="75" cy="86" r="5"/><rect x="70" y="91" width="10" height="16"/><circle cx="95" cy="84" r="5"/><rect x="90" y="89" width="10" height="18"/></g>'
            .'<text x="6" y="14" font-family="monospace" font-size="9" fill="#fff">'.$t.'</text></svg>';
    }
}
