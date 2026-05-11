<?php

namespace App\Services;

use App\Models\Order;

class InvoiceImageService
{
    private int $padding = 12;
    private int $font = 3;
    private int $charsPerLine = 44;

    /**
     * Generate a PNG invoice image for the given order and return it base64-encoded.
     */
    public function generateBase64(Order $order): string
    {
        $order->loadMissing(['member', 'items', 'user']);

        $lines = $this->buildReceiptLines($order);
        $lineHeight = imagefontheight($this->font) + 6;
        $charWidth = imagefontwidth($this->font);
        $contentWidth = $charWidth * $this->charsPerLine;

        $logoPath = public_path('logo-sheza-1bit.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-sheza.png');
        }

        $logo = null;
        $logoWidth = 0;
        $logoHeight = 0;
        if (file_exists($logoPath)) {
            $logo = @imagecreatefrompng($logoPath);
            if ($logo !== false) {
                $logoWidth = imagesx($logo);
                $logoHeight = imagesy($logo);
                $maxLogoWidth = 180;
                if ($logoWidth > $maxLogoWidth) {
                    $ratio = $maxLogoWidth / $logoWidth;
                    $logoWidth = (int) floor($logoWidth * $ratio);
                    $logoHeight = (int) floor($logoHeight * $ratio);
                }
            } else {
                $logo = null;
            }
        }

        $qrisPath = public_path('qris-sheza.png');
        $qris = null;
        $qrisWidth = 0;
        $qrisHeight = 0;
        if (file_exists($qrisPath)) {
            $qris = @imagecreatefrompng($qrisPath);
            if ($qris !== false) {
                $qrisWidth = imagesx($qris);
                $qrisHeight = imagesy($qris);
                $maxQrisWidth = min(260, $contentWidth);
                if ($qrisWidth > $maxQrisWidth) {
                    $ratio = $maxQrisWidth / $qrisWidth;
                    $qrisWidth = (int) floor($qrisWidth * $ratio);
                    $qrisHeight = (int) floor($qrisHeight * $ratio);
                }
            } else {
                $qris = null;
            }
        }

        $contentBlockWidth = max($contentWidth, $logoWidth, $qrisWidth);
        $canvasWidth = $contentBlockWidth + ($this->padding * 2);
        $height = $this->padding
            + ($logo ? $logoHeight + 14 : 0)
            + (count($lines) * $lineHeight)
            + ($qris ? (24 + $lineHeight + $qrisHeight) : 0)
            + $this->padding;

        $img = imagecreatetruecolor($canvasWidth, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 25, 25, 25);
        imagefill($img, 0, 0, $white);

        $contentX = (int) floor(($canvasWidth - $contentWidth) / 2);
        $y = $this->padding;

        if ($logo) {
            $logoX = (int) floor(($canvasWidth - $logoWidth) / 2);

            if ($logoWidth !== imagesx($logo) || $logoHeight !== imagesy($logo)) {
                imagecopyresampled(
                    $img,
                    $logo,
                    $logoX,
                    $y,
                    0,
                    0,
                    $logoWidth,
                    $logoHeight,
                    imagesx($logo),
                    imagesy($logo)
                );
            } else {
                imagecopy($img, $logo, $logoX, $y, 0, 0, $logoWidth, $logoHeight);
            }

            imagedestroy($logo);
            $y += $logoHeight + 14;
        }

        foreach ($lines as $line) {
            imagestring($img, $this->font, $contentX, $y, $line, $black);
            $y += $lineHeight;
        }

        if ($qris) {
            $y += 12;
            $caption = 'Scan QRIS untuk pembayaran';
            $captionX = (int) floor(($canvasWidth - (strlen($caption) * $charWidth)) / 2);
            imagestring($img, $this->font, $captionX, $y, $caption, $black);
            $y += $lineHeight;

            $qrisX = (int) floor(($canvasWidth - $qrisWidth) / 2);
            if ($qrisWidth !== imagesx($qris) || $qrisHeight !== imagesy($qris)) {
                imagecopyresampled(
                    $img,
                    $qris,
                    $qrisX,
                    $y,
                    0,
                    0,
                    $qrisWidth,
                    $qrisHeight,
                    imagesx($qris),
                    imagesy($qris)
                );
            } else {
                imagecopy($img, $qris, $qrisX, $y, 0, 0, $qrisWidth, $qrisHeight);
            }

            imagedestroy($qris);
        }

        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return base64_encode($data);
    }

    private function buildReceiptLines(Order $order): array
    {
        $w = $this->charsPerLine;
        $itemCol = 22;
        $qtyCol = 6;
        $priceCol = 7;
        $gapCol = 1;
        $totalCol = 8;
        $sep = str_repeat('-', $w);

        $createdAt = $order->created_at->translatedFormat('d F Y H:i');
        $finishAt = $order->ready_at
            ? $order->ready_at->translatedFormat('j F Y')
            : $order->created_at->copy()->addDays($order->is_express ? 1 : 3)->translatedFormat('j F Y');

        $cashier = $order->user?->name ?? 'Admin';
        $customer = strtoupper($order->member?->name ?? 'TAMU');

        $lines = [
            $this->center('SHEZA LAUNDRY SOLO', $w),
            $this->center('Jl. Pucangsawit RT 03 RW 03', $w),
            $this->center('Kec. Jebres, Surakarta', $w),
            $this->center('Tel: +62 8820-0933-4660', $w),
            $sep,
            $this->cols2('Order ID :', $order->order_number, $w),
            $this->cols2('Tanggal  :', $createdAt, $w),
            $this->cols2('Kasir    :', $cashier, $w),
            $this->cols2('Pelanggan:', $customer, $w),
            $sep,
            $this->rpad('ITEM', $itemCol)
                . $this->rpad('QTY', $qtyCol)
                . $this->lpad('HARGA', $priceCol)
                . str_repeat(' ', $gapCol)
                . $this->lpad('SUBTOTAL', $totalCol),
            $sep,
        ];

        foreach ($order->items as $item) {
            $qty = $item->service_type === 'kiloan'
                ? (($item->weight ? $item->weight . 'kg' : '?kg'))
                : (intval($item->quantity) . 'x');

            $price = number_format((float) $item->price, 0, ',', '.');
            $subtotal = ($item->service_type === 'kiloan' && !$item->weight)
                ? 'TBD'
                : number_format((float) $item->subtotal, 0, ',', '.');

            $name = $this->truncate($item->service_name, $itemCol);

            $lines[] = $this->rpad($name, $itemCol)
                . $this->rpad($qty, $qtyCol)
                . $this->lpad($price, $priceCol)
                . str_repeat(' ', $gapCol)
                . $this->lpad($subtotal, $totalCol);
        }

        $lines[] = $sep;
        $lines[] = $this->cols2('TOTAL    :', 'Rp ' . number_format((float) $order->total, 0, ',', '.'), $w);

        if ($order->payment_status === 'lunas') {
            $lines[] = $sep;
            $lines[] = $this->cols2(
                'DIBAYAR (' . strtoupper($order->payment_method ?? 'tunai') . '):',
                'Rp ' . number_format((float) ($order->paid_amount ?? $order->total), 0, ',', '.'),
                $w
            );
        }

        $lines[] = $sep;
        $lines[] = $this->cols2('Tgl Selesai:', $finishAt, $w);
        $lines[] = $sep;
        $lines[] = $this->center('TERIMAKASIH', $w);
        $lines[] = $this->center('Tidak Menerima Laundry Pakaian Dalam', $w);
        $lines[] = $this->center('Menerima Laundry Alat Gunung', $w);
        $lines[] = $this->center('IG : @sheza_laundrysolo', $w);
        $lines[] = $this->center('IG : @krabatadventure', $w);
        $lines[] = $sep;
        $lines[] = $this->center('Dicetak: ' . now()->translatedFormat('d F Y H:i'), $w);

        return $lines;
    }

    private function rpad(string $s, int $n): string
    {
        $s = trim($s);
        return mb_strlen($s) >= $n ? mb_substr($s, 0, $n) : str_pad($s, $n, ' ', STR_PAD_RIGHT);
    }

    private function lpad(string $s, int $n): string
    {
        $s = trim($s);
        return mb_strlen($s) >= $n ? mb_substr($s, 0, $n) : str_pad($s, $n, ' ', STR_PAD_LEFT);
    }

    private function cols2(string $a, string $b, int $w): string
    {
        $a = trim($a);
        $b = trim($b);

        if ((mb_strlen($a) + mb_strlen($b) + 1) > $w) {
            $a = mb_substr($a, 0, max(0, $w - mb_strlen($b) - 1));
        }

        $space = max(1, $w - mb_strlen($a) - mb_strlen($b));
        return $a . str_repeat(' ', $space) . $b;
    }

    private function center(string $s, int $w): string
    {
        $s = trim($s);
        if (mb_strlen($s) >= $w) {
            return mb_substr($s, 0, $w);
        }

        $pad = (int) floor(($w - mb_strlen($s)) / 2);
        return str_repeat(' ', $pad) . $s;
    }

    private function truncate(string $str, int $max): string
    {
        if (mb_strlen($str) <= $max) {
            return $str;
        }

        if ($max <= 3) {
            return mb_substr($str, 0, $max);
        }

        return mb_substr($str, 0, $max - 3) . '...';
    }
}
