<?php

namespace App\Services;

class KodeVerifikasiGenerator
{
    private const ALFABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generate(): string
    {
        $kode = '';

        for ($i = 0; $i < 8; $i++) {
            $kode .= self::ALFABET[random_int(0, strlen(self::ALFABET) - 1)];
        }

        return 'GS-'.$kode;
    }
}
