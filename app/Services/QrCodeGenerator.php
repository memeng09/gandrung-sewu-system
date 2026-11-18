<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeGenerator
{
    public function dataUri(string $data): string
    {
        return Builder::create()
            ->writer(new PngWriter)
            ->data($data)
            ->size(400)
            ->margin(0)
            ->build()
            ->getDataUri();
    }
}
