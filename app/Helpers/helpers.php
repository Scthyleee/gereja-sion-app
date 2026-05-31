<?php

if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs((int)$angka);
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($angka < 12) return $huruf[$angka];
        if ($angka < 20) return terbilang($angka - 10) . ' belas';
        if ($angka < 100) return terbilang(intdiv($angka, 10)) . ' puluh' . ($angka % 10 ? ' ' . terbilang($angka % 10) : '');
        if ($angka < 200) return 'seratus' . ($angka % 100 ? ' ' . terbilang($angka % 100) : '');
        if ($angka < 1000) return terbilang(intdiv($angka, 100)) . ' ratus' . ($angka % 100 ? ' ' . terbilang($angka % 100) : '');
        if ($angka < 2000) return 'seribu' . ($angka % 1000 ? ' ' . terbilang($angka % 1000) : '');
        if ($angka < 1000000) return terbilang(intdiv($angka, 1000)) . ' ribu' . ($angka % 1000 ? ' ' . terbilang($angka % 1000) : '');
        if ($angka < 1000000000) return terbilang(intdiv($angka, 1000000)) . ' juta' . ($angka % 1000000 ? ' ' . terbilang($angka % 1000000) : '');
        if ($angka < 1000000000000) return terbilang(intdiv($angka, 1000000000)) . ' miliar' . ($angka % 1000000000 ? ' ' . terbilang($angka % 1000000000) : '');

        return terbilang(intdiv($angka, 1000000000000)) . ' triliun' . ($angka % 1000000000000 ? ' ' . terbilang($angka % 1000000000000) : '');
    }
}
