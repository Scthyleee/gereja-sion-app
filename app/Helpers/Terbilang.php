<?php

namespace App\Helpers;

class Terbilang
{
    private static $angka = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

    public static function convert($n): string
    {
        $n = abs((int) $n);
        if ($n < 12) return self::$angka[$n];
        if ($n < 20) return self::$angka[$n - 10] . ' Belas';
        if ($n < 100) return self::$angka[(int)($n / 10)] . ' Puluh' . ($n % 10 ? ' ' . self::$angka[$n % 10] : '');
        if ($n < 200) return 'Seratus' . ($n - 100 > 0 ? ' ' . self::convert($n - 100) : '');
        if ($n < 1000) return self::$angka[(int)($n / 100)] . ' Ratus' . ($n % 100 ? ' ' . self::convert($n % 100) : '');
        if ($n < 2000) return 'Seribu' . ($n - 1000 > 0 ? ' ' . self::convert($n - 1000) : '');
        if ($n < 1000000) return self::convert((int)($n / 1000)) . ' Ribu' . ($n % 1000 ? ' ' . self::convert($n % 1000) : '');
        if ($n < 1000000000) return self::convert((int)($n / 1000000)) . ' Juta' . ($n % 1000000 ? ' ' . self::convert($n % 1000000) : '');
        if ($n < 1000000000000) return self::convert((int)($n / 1000000000)) . ' Miliar' . ($n % 1000000000 ? ' ' . self::convert($n % 1000000000) : '');
        return self::convert((int)($n / 1000000000000)) . ' Triliun' . ($n % 1000000000000 ? ' ' . self::convert($n % 1000000000000) : '');
    }

    public static function rupiah($n): string
    {
        return self::convert($n) . ' Rupiah';
    }
}
