<?php

namespace App\Services;

use App\Models\Order;

class InvoiceImageService
{
    private int $width   = 520;
    private int $padding = 24;
    private string $fontRegular;
    private string $fontBold;

    public function __construct()
    {
        $this->fontRegular = $this->findFont(false);
        $this->fontBold    = $this->findFont(true);
    }

    /**
     * Generate a PNG invoice image for the given order and return it base64-encoded.
     */
    public function generateBase64(Order $order): string
    {
        $order->loadMissing(['member', 'items']);

        $items    = $order->items;
        $hasNotes = !empty($order->notes);

        // Dynamic height
        $height = 340 + ($items->count() * 42) + ($hasNotes ? 32 : 0) + 40;

        $img = imagecreatetruecolor($this->width, $height);

        // ── Colour palette ───────────────────────────────────────────────────
        $cWhite      = imagecolorallocate($img, 255, 255, 255);
        $cBlack      = imagecolorallocate($img, 26,  26,  26);
        $cDark       = imagecolorallocate($img, 50,  50,  50);
        $cGray       = imagecolorallocate($img, 130, 130, 130);
        $cAmber      = imagecolorallocate($img, 245, 158, 11);
        $cAmberLight = imagecolorallocate($img, 255, 236, 153);
        $cBg         = imagecolorallocate($img, 255, 251, 235);
        $cBorder     = imagecolorallocate($img, 229, 229, 229);
        $cShadow     = imagecolorallocate($img, 215, 200, 180);

        // ── Background ───────────────────────────────────────────────────────
        imagefill($img, 0, 0, $cWhite);

        // ── Header bar ───────────────────────────────────────────────────────
        imagefilledrectangle($img, 0, 0, $this->width, 76, $cAmber);

        $useTtf = $this->fontRegular !== '' && $this->fontBold !== '';

        if ($useTtf) {
            imagettftext($img, 21, 0, $this->padding, 42, $cWhite,      $this->fontBold,    'SHEZA LAUNDRY');
            imagettftext($img, 10, 0, $this->padding, 65, $cAmberLight, $this->fontRegular, 'Nota / Invoice Laundry');
        } else {
            imagestring($img, 5, $this->padding, 14, 'SHEZA LAUNDRY',          $cWhite);
            imagestring($img, 3, $this->padding, 40, 'Nota / Invoice Laundry', $cAmberLight);
        }

        $y = 90;

        // ── Meta rows ────────────────────────────────────────────────────────
        $y = $this->row($img, $y, 'No. Order',  $order->order_number,                              $cGray, $cAmber, $useTtf);
        $y = $this->row($img, $y, 'Tanggal',    $order->created_at->format('d M Y, H:i'),          $cGray, $cDark,  $useTtf);
        $y = $this->row($img, $y, 'Pelanggan',  $order->member?->name ?? 'Tamu',                   $cGray, $cDark,  $useTtf);

        if ($order->member?->phone) {
            $y = $this->row($img, $y, 'No. HP', $order->member->phone, $cGray, $cDark, $useTtf);
        }

        if ($order->has_kiloan && (float) ($order->weight ?? 0) > 0) {
            $y = $this->row(
                $img,
                $y,
                'Berat',
                number_format((float) $order->weight, 1, ',', '.') . ' kg',
                $cGray,
                $cDark,
                $useTtf
            );
        }

        if ($order->is_express ?? false) {
            $y = $this->row($img, $y, 'Jenis', 'EXPRESS', $cGray, $cAmber, $useTtf);
        }

        $y += 8;
        $this->dashed($img, $y, $cBorder);
        $y += 14;

        // ── Items header ─────────────────────────────────────────────────────
        if ($useTtf) {
            imagettftext($img, 9, 0, $this->padding, $y + 11, $cGray, $this->fontBold, 'ITEM PESANAN');
        } else {
            imagestring($img, 2, $this->padding, $y, 'ITEM PESANAN', $cGray);
        }
        $y += 16;
        $this->dashed($img, $y, $cBorder);
        $y += 10;

        // ── Items list ───────────────────────────────────────────────────────
        foreach ($items as $item) {
            $name = $this->truncate($item->service_name, 36);

            if ($item->service_type === 'kiloan') {
                $qty = number_format((float) ($item->weight ?? $item->quantity), 1, ',', '.') . ' kg';
            } else {
                $qty = number_format((int) $item->quantity, 0) . ' pcs';
            }

            $price    = 'Rp ' . number_format((float) $item->price, 0, ',', '.');
            $subtotal = 'Rp ' . number_format((float) $item->subtotal, 0, ',', '.');

            if ($useTtf) {
                imagettftext($img, 10, 0, $this->padding, $y + 13, $cBlack, $this->fontBold,    $name);
                imagettftext(
                    $img,
                    9,
                    0,
                    $this->padding + 10,
                    $y + 27,
                    $cGray,
                    $this->fontRegular,
                    $qty . ' × ' . $price
                );

                // Right-align subtotal
                $box  = imagettfbbox(10, 0, $this->fontBold, $subtotal);
                $tw   = abs($box[4] - $box[0]);
                imagettftext(
                    $img,
                    10,
                    0,
                    $this->width - $this->padding - $tw,
                    $y + 13,
                    $cDark,
                    $this->fontBold,
                    $subtotal
                );
            } else {
                imagestring($img, 3, $this->padding, $y + 2,  $name,                    $cBlack);
                imagestring($img, 2, $this->padding + 8, $y + 16, $qty . ' x ' . $price, $cGray);
                imagestring($img, 3, $this->width - 120, $y + 2, $subtotal,              $cDark);
            }

            $y += 40;
        }

        $y += 4;
        $this->dashed($img, $y, $cBorder);
        $y += 10;

        // ── Total block ──────────────────────────────────────────────────────
        imagefilledrectangle($img, $this->padding - 8, $y, $this->width - $this->padding + 8, $y + 44, $cBg);

        $totalText = 'Rp ' . number_format((float) $order->total, 0, ',', '.');

        if ($useTtf) {
            imagettftext($img, 12, 0, $this->padding, $y + 28, $cDark, $this->fontBold, 'TOTAL');

            $box = imagettfbbox(15, 0, $this->fontBold, $totalText);
            $tw  = abs($box[4] - $box[0]);
            imagettftext(
                $img,
                15,
                0,
                $this->width - $this->padding - $tw,
                $y + 30,
                $cAmber,
                $this->fontBold,
                $totalText
            );
        } else {
            imagestring($img, 4, $this->padding, $y + 14, 'TOTAL: ' . $totalText, $cAmber);
        }

        $y += 56;

        // ── Notes ────────────────────────────────────────────────────────────
        if ($hasNotes) {
            if ($useTtf) {
                imagettftext(
                    $img,
                    9,
                    0,
                    $this->padding,
                    $y + 12,
                    $cGray,
                    $this->fontRegular,
                    'Catatan: ' . $this->truncate($order->notes, 60)
                );
            } else {
                imagestring($img, 2, $this->padding, $y, 'Catatan: ' . $order->notes, $cGray);
            }
            $y += 32;
        }

        $this->dashed($img, $y, $cBorder);
        $y += 16;

        // ── Footer ───────────────────────────────────────────────────────────
        $footer = 'Terima kasih telah mempercayakan laundry Anda kepada kami!';

        if ($useTtf) {
            $box = imagettfbbox(9, 0, $this->fontRegular, $footer);
            $tw  = abs($box[4] - $box[0]);
            imagettftext(
                $img,
                9,
                0,
                (int) (($this->width - $tw) / 2),
                $y + 14,
                $cGray,
                $this->fontRegular,
                $footer
            );
        } else {
            imagestring($img, 2, $this->padding, $y, $footer, $cGray);
        }

        // ── Capture PNG ──────────────────────────────────────────────────────
        ob_start();
        imagepng($img);
        $data = ob_get_clean();
        imagedestroy($img);

        return base64_encode($data);
    }

    // ────────────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────────────

    private function row(
        $img,
        int $y,
        string $label,
        string $value,
        $labelColor,
        $valueColor,
        bool $useTtf
    ): int {
        if ($useTtf) {
            imagettftext($img, 9,  0, $this->padding, $y + 13, $labelColor, $this->fontRegular, $label . ':');
            imagettftext($img, 10, 0, 170,            $y + 13, $valueColor, $this->fontBold,    $value);
        } else {
            imagestring($img, 2, $this->padding, $y, $label . ': ' . $value, $labelColor);
        }
        return $y + 22;
    }

    private function dashed($img, int $y, $color): void
    {
        $dash = 6;
        $gap = 4;
        for ($x = $this->padding; $x < $this->width - $this->padding; $x += $dash + $gap) {
            imageline($img, $x, $y, min($x + $dash - 1, $this->width - $this->padding), $y, $color);
        }
    }

    private function findFont(bool $bold): string
    {
        $paths = $bold
            ? [
                'C:/Windows/Fonts/calibrib.ttf',
                'C:/Windows/Fonts/arialbd.ttf',
                'C:/Windows/Fonts/verdanab.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                'C:/Windows/Fonts/calibri.ttf',
                'C:/Windows/Fonts/arial.ttf',
                'C:/Windows/Fonts/verdana.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans.ttf',
            ];

        foreach ($paths as $p) {
            if (file_exists($p)) return $p;
        }
        return '';
    }

    private function truncate(string $str, int $max): string
    {
        if (mb_strlen($str) <= $max) return $str;
        return mb_substr($str, 0, $max - 3) . '...';
    }
}
