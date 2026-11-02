<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Semua ikon SVG situs dalam satu tempat. Dipakai lewat komponen <x-icon name="..." />.
 *
 * Menambah ikon: tambahkan satu entri di ICONS. Kunci "attrs" adalah atribut <svg>,
 * "body" isi gambarnya, dan "class" ukuran bawaan dengan "classMode":
 *  - "merge": kelas bawaan selalu dipasang, kelas dari pemanggil ditambahkan di belakangnya;
 *  - "fallback": kelas bawaan hanya dipakai bila pemanggil tidak memberi kelas;
 *  - null: tanpa kelas bawaan (ukuran ditentukan pemanggil).
 * Ikon kategori yang bisa dipilih admin terdaftar di CategoryIcons dan harus ada di sini.
 */
class Icons
{
    private const ICONS = [
        'arrow-down' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => 'h-4 w-4',
            'classMode' => 'merge',
            'aria' => true,
            'body' => '<path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />',
        ],
        'arrow-left' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => 'h-6 w-6',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path d="M19 12H5m7 7-7-7 7-7" />',
        ],
        'arrow-right' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => 'h-4 w-4',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />',
        ],
        'blocks' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<rect x="3" y="12" width="8" height="8" rx="1" /> <rect x="13" y="12" width="8" height="8" rx="1" /> <rect x="8" y="4" width="8" height="8" rx="1" />',
        ],
        'bolt' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z" />',
        ],
        'briefcase' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<rect x="3" y="7" width="18" height="13" rx="1.5" /> <path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" /> <path d="M3 13h18" /> <path d="M11 12h2v2h-2z" />',
        ],
        'building' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M4 21V4h10v17" /> <path d="M14 9h6v12" /> <path d="M2 21h20" /> <path d="M8 8h2M8 12h2M8 16h2" /> <path d="M17 13h.01M17 17h.01" />',
        ],
        'cart' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M3 4h2.5l2 11h10.5l2-8H6.5" /> <circle cx="9" cy="19" r="1.5" /> <circle cx="17" cy="19" r="1.5" />',
        ],
        'chip' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<rect x="6" y="6" width="12" height="12" rx="1" /> <rect x="9.5" y="9.5" width="5" height="5" /> <path d="M9 3v3M15 3v3M9 18v3M15 18v3M3 9h3M3 15h3M18 9h3M18 15h3" />',
        ],
        'close' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => null,
            'classMode' => null,
            'aria' => false,
            'body' => '<path d="M6 6l12 12M18 6 6 18" />',
        ],
        'computer' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<rect x="3" y="4" width="18" height="13" rx="1" /> <path d="M8 21h8" /> <path d="M12 17v4" /> <path d="M7 8h4" /> <path d="M7 11h7" />',
        ],
        'dashboard' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => null,
            'classMode' => null,
            'aria' => true,
            'body' => '<rect x="3" y="3" width="7" height="9" rx="1" /> <rect x="14" y="3" width="7" height="5" rx="1" /> <rect x="14" y="12" width="7" height="9" rx="1" /> <rect x="3" y="16" width="7" height="5" rx="1" />',
        ],
        'error-404' => [
            'attrs' => ['fill' => 'none', 'viewBox' => '0 0 760 320', 'aria-hidden' => 'true'],
            'class' => null,
            'classMode' => null,
            'aria' => false,
            'body' => '<path d="M45 215 L175 45 V265 M45 215 H205" stroke="currentColor" stroke-width="15" stroke-linecap="square" stroke-linejoin="miter" /> <circle cx="380" cy="155" r="105" stroke="currentColor" stroke-width="15" /> <path d="M306 81 L454 229" stroke="currentColor" stroke-width="15" stroke-linecap="round" /> <path d="M555 215 L685 45 V265 M555 215 H715" stroke="currentColor" stroke-width="15" stroke-linecap="square" stroke-linejoin="miter" />',
        ],
        'error-419' => [
            'attrs' => ['fill' => 'none', 'viewBox' => '0 0 760 320', 'aria-hidden' => 'true'],
            'class' => null,
            'classMode' => null,
            'aria' => false,
            'body' => '<path d="M45 215 L175 45 V265 M45 215 H205" stroke="currentColor" stroke-width="15" stroke-linecap="square" stroke-linejoin="miter" /> <path d="M346.5 100 L406.5 45 V265" stroke="currentColor" stroke-width="15" stroke-linecap="square" stroke-linejoin="miter" /> <circle cx="630" cy="135" r="85" stroke="currentColor" stroke-width="15" /> <path d="M715 135 V195 A70 70 0 0 1 645 265 H570" stroke="currentColor" stroke-width="15" stroke-linecap="square" stroke-linejoin="round" />',
        ],
        'error-500' => [
            'attrs' => ['fill' => 'none', 'viewBox' => '0 0 760 320', 'aria-hidden' => 'true'],
            'class' => null,
            'classMode' => null,
            'aria' => false,
            'body' => '<path d="M245 45 H65.8 V120 A105 85 0 1 1 65.8 240" stroke="currentColor" stroke-width="15" stroke-linecap="square" stroke-linejoin="miter" /> <circle cx="385" cy="155" r="105" stroke="currentColor" stroke-width="15" /> <path d="M311 81 L459 229" stroke="currentColor" stroke-width="15" stroke-linecap="round" /> <circle cx="635" cy="155" r="105" stroke="currentColor" stroke-width="15" /> <path d="M635 98 V175" stroke="currentColor" stroke-width="15" stroke-linecap="square" /> <circle cx="635" cy="217" r="9" fill="currentColor" />',
        ],
        'gearcog' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '2.5', 'viewBox' => '0 0 48 48'],
            'class' => 'h-12 w-12 translate-y-1 transition-transform duration-300 group-hover:translate-y-0',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path transform="translate(0 2.8)" d="M20.6 5.2h6.8l1.1 4.1a15 15 0 0 1 3.3 1.9l4-1.4 3.4 5.9-3 3a15 15 0 0 1 0 3.8l3 3-3.4 5.9-4-1.4a15 15 0 0 1-3.3 1.9l-1.1 4.1h-6.8l-1.1-4.1a15 15 0 0 1-3.3-1.9l-4 1.4-3.4-5.9 3-3a15 15 0 0 1 0-3.8l-3-3 3.4-5.9 4 1.4a15 15 0 0 1 3.3-1.9l1.1-4.1Z" /> <circle cx="24" cy="24" r="6.5" />',
        ],
        'globe' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<circle cx="12" cy="12" r="9" /> <path d="M3 12h18" /> <path d="M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3Z" />',
        ],
        'hammer' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="m4 20 8.5-8.5" /> <path d="m11 7 3-3 6 6-3 3z" /> <path d="m9 9 3-3" />',
        ],
        'house' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path d="M3 10.5 12 3l9 7.5" /> <path d="M5 9.5V21h14V9.5" /> <path d="M9 21v-6h6v6" />',
        ],
        'lightbulb' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1h5c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3Z" /> <path d="M9.5 18.5h5" /> <path d="M10.5 21h3" />',
        ],
        'mail' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.6', 'viewBox' => '0 0 24 24'],
            'class' => 'h-5 w-5',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<rect x="3" y="5" width="18" height="14" rx="1.5" /> <path d="m3 7 9 6 9-6" />',
        ],
        'maps' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.6', 'viewBox' => '0 0 24 24'],
            'class' => 'h-5 w-5',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path d="M20 10.5c0 5.5-8 10-8 10s-8-4.5-8-10a8 8 0 1 1 16 0Z" /> <circle cx="12" cy="10.5" r="2.5" />',
        ],
        'menu' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => 'h-5 w-5',
            'classMode' => 'merge',
            'aria' => true,
            'body' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />',
        ],
        'music' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M9 18V5l11-2v13" /> <circle cx="6.5" cy="18" r="2.5" /> <circle cx="17.5" cy="16" r="2.5" />',
        ],
        'package' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Z" /> <path d="m3 7.5 9 4.5 9-4.5" /> <path d="M12 12v9" /> <path d="m7.5 5.25 9 4.5" />',
        ],
        'pencil' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="m4 20 1-4L16.5 4.5a2.1 2.1 0 0 1 3 3L8 19l-4 1Z" /> <path d="m14.5 6.5 3 3" />',
        ],
        'phone' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.6', 'viewBox' => '0 0 24 24'],
            'class' => 'h-5 w-5',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path d="M6.6 3.5h2.2l1.3 5-2.2 1.8a14.5 14.5 0 0 0 5.8 5.8l1.8-2.2 5 1.3v2.2c0 1-.8 1.8-1.8 1.8C11.3 19.2 4.8 12.7 4.8 5.3c0-1 .8-1.8 1.8-1.8Z" />',
        ],
        'shield' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="m12 3 8 3v6c0 4.5-3.3 8.2-8 9-4.7-.8-8-4.5-8-9V6l8-3Z" /> <path d="m8.5 12 2.5 2.5 4.5-5" />',
        ],
        'snowflake' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M12 2v20" /> <path d="m3.3 7 17.4 10" /> <path d="M3.3 17 20.7 7" /> <path d="m9.5 3.8 2.5 2.2 2.5-2.2" /> <path d="m9.5 20.2 2.5-2.2 2.5 2.2" />',
        ],
        'sports' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<circle cx="12" cy="12" r="8.5" /> <path d="M8 9.5c1.5-1.5 3.5-1.5 5 0s3.5 1.5 5 0" /> <path d="M7.5 14.5c1.5 1.5 3.5 1.5 5 0s3.5-1.5 5-0" />',
        ],
        'truck' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M2 6h11v10H2z" /> <path d="M13 9h4.5l3.5 3.5V16h-8" /> <circle cx="6.5" cy="17.5" r="1.8" /> <circle cx="17" cy="17.5" r="1.8" />',
        ],
        'utensils' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'fallback',
            'aria' => true,
            'body' => '<path d="M6 3v7a2 2 0 0 0 4 0V3" /> <path d="M8 3v18" /> <path d="M17 21V3c-2 1.5-3 4-3 7v3h3" />',
        ],
        'warehouse' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.4', 'viewBox' => '0 0 24 24'],
            'class' => 'h-11 w-11',
            'classMode' => 'merge',
            'aria' => false,
            'body' => '<path d="M4 20V9l8-5 8 5v11" /> <path d="M8 20v-5h8v5" /> <path d="M8 9h8" />',
        ],
        'zoom-in' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => null,
            'classMode' => null,
            'aria' => false,
            'body' => '<circle cx="11" cy="11" r="6.5" /> <path d="m16 16 4.5 4.5" /> <path d="M8.5 11h5M11 8.5v5" />',
        ],
        'zoom-out' => [
            'attrs' => ['fill' => 'none', 'stroke' => 'currentColor', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'stroke-width' => '1.8', 'viewBox' => '0 0 24 24'],
            'class' => null,
            'classMode' => null,
            'aria' => false,
            'body' => '<circle cx="11" cy="11" r="6.5" /> <path d="m16 16 4.5 4.5" /> <path d="M8.5 11h5" />',
        ],
    ];

    /**
     * @return array{attrs: array<string, string>, class: ?string, classMode: ?string, aria: bool, body: string}
     */
    public static function get(string $name): array
    {
        return self::ICONS[$name] ?? throw new InvalidArgumentException("Ikon \"{$name}\" belum didaftarkan di App\Support\Icons.");
    }

    public static function has(string $name): bool
    {
        return isset(self::ICONS[$name]);
    }

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return array_keys(self::ICONS);
    }
}
