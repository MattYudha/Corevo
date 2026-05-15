<?php

namespace App\Helpers;

class Terbilang
{
    // main function
    public static function make($angka)
    {
        // generate text and clean extra spaces
        $hasil = trim(self::penyebut($angka));

        return preg_replace('/\s+/', ' ', $hasil);
    }

    // recursive number conversion function
    private static function penyebut($angka)
    {
        // force integer value to avoid decimal output
        $angka = abs((int)$angka);

        $baca = array(
            "",
            "Satu",
            "Dua",
            "Tiga",
            "Empat",
            "Lima",
            "Enam",
            "Tujuh",
            "Delapan",
            "Sembilan",
            "Sepuluh",
            "Sebelas"
        );

        $terbilang = "";

        if ($angka < 12) {

            $terbilang = " " . $baca[$angka];

        } elseif ($angka < 20) {

            $terbilang = self::penyebut($angka - 10) . " Belas";

        } elseif ($angka < 100) {

            $terbilang =
                self::penyebut((int)($angka / 10)) .
                " Puluh" .
                self::penyebut($angka % 10);

        } elseif ($angka < 200) {

            $terbilang =
                " Seratus" .
                self::penyebut($angka - 100);

        } elseif ($angka < 1000) {

            $terbilang =
                self::penyebut((int)($angka / 100)) .
                " Ratus" .
                self::penyebut($angka % 100);

        } elseif ($angka < 2000) {

            $terbilang =
                " Seribu" .
                self::penyebut($angka - 1000);

        } elseif ($angka < 1000000) {

            $terbilang =
                self::penyebut((int)($angka / 1000)) .
                " Ribu" .
                self::penyebut($angka % 1000);

        } elseif ($angka < 1000000000) {

            $terbilang =
                self::penyebut((int)($angka / 1000000)) .
                " Juta" .
                self::penyebut($angka % 1000000);

        } elseif ($angka < 1000000000000) {

            $terbilang =
                self::penyebut((int)($angka / 1000000000)) .
                " Milyar" .
                self::penyebut(fmod($angka, 1000000000));

        } elseif ($angka < 1000000000000000) {

            // trillion support
            $terbilang =
                self::penyebut((int)($angka / 1000000000000)) .
                " Triliun" .
                self::penyebut(fmod($angka, 1000000000000));
        }

        // keep spacing for combined words
        return $terbilang;
    }
}