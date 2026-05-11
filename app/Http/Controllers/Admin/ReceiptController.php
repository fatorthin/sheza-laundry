<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['member', 'items.service', 'user']);
        return view('admin.receipt', compact('order'));
    }

    /**
     * Serve raw ESC/POS binary for RawBT via signed URL.
     * RawBT fetches this directly — no URL-encoding corruption.
     */
    public function printEscPos(Order $order): Response
    {
        $data = $this->buildEscPosBinary($order);

        return response($data, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="receipt.bin"',
            'Cache-Control'       => 'no-store',
        ]);
    }

    /**
     * Build ESC/POS and save as downloadable .bin file.
     */
    public function saveEscPos(Order $order): JsonResponse
    {
        $data = $this->buildEscPosBinary($order);

        $dir = public_path('temp/escpos');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $safeOrderNumber = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $order->order_number);
        $safeOrderNumber = trim($safeOrderNumber ?? 'order', '-');
        if ($safeOrderNumber === '') {
            $safeOrderNumber = 'order';
        }

        $filename = 'receipt-' . $safeOrderNumber . '-' . now()->format('YmdHis') . '.bin';
        $filePath = $dir . DIRECTORY_SEPARATOR . $filename;

        File::put($filePath, $data);

        return response()->json([
            'ok' => true,
            'file_name' => $filename,
            'file_url' => url('temp/escpos/' . $filename),
            'bytes' => strlen($data),
        ]);
    }

    private function buildEscPosBinary(Order $order): string
    {
        $order->load(['member', 'items.service', 'user']);

        $ESC = "\x1B";
        $GS  = "\x1D";
        $LF  = "\n";
        $W   = 32;

        $sep = str_repeat('-', $W);

        $rpad = fn(string $s, int $n): string =>
        mb_strlen($s) >= $n ? mb_substr($s, 0, $n) : $s . str_repeat(' ', $n - mb_strlen($s));

        $lpad = fn(string $s, int $n): string =>
        mb_strlen($s) >= $n ? mb_substr($s, 0, $n) : str_repeat(' ', $n - mb_strlen($s)) . $s;

        $cols2 = function (string $a, string $b) use ($W): string {
            if (mb_strlen($a) + mb_strlen($b) + 1 > $W) {
                $a = mb_substr($a, 0, $W - mb_strlen($b) - 1);
            }
            return $a . str_repeat(' ', $W - mb_strlen($a) - mb_strlen($b)) . $b;
        };

        $center = function (string $s) use ($W): string {
            $pad = max(0, (int) floor(($W - mb_strlen($s)) / 2));
            return str_repeat(' ', $pad) . $s;
        };

        $data = '';

        // ── Init + center align ────────────────────────────────────────
        $data .= $ESC . '@';
        $data .= $ESC . 'a' . "\x01";

        // ── Logo raster ────────────────────────────────────────────────
        $logoPath = public_path('logo-sheza-1bit.png');
        if (file_exists($logoPath)) {
            $img = @imagecreatefrompng($logoPath);
            if ($img) {
                $origW = imagesx($img);
                $origH = imagesy($img);
                // Use 192px for clearer logo on 58mm paper.
                $maxW  = 192;
                $ratio = $origW > $maxW ? $maxW / $origW : 1.0;
                $w     = max(1, (int) floor($origW * $ratio));
                $h     = max(1, (int) floor($origH * $ratio));

                $thumb = imagecreatetruecolor($w, $h);
                imagefill($thumb, 0, 0, imagecolorallocate($thumb, 255, 255, 255));
                imagecopyresampled($thumb, $img, 0, 0, 0, 0, $w, $h, $origW, $origH);
                imagedestroy($img);

                $widthBytes  = (int) ceil($w / 8);
                $rasterBytes = str_repeat("\x00", $widthBytes * $h);

                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        $rgb = imagecolorat($thumb, $x, $y);
                        $r   = ($rgb >> 16) & 0xFF;
                        $g   = ($rgb >>  8) & 0xFF;
                        $b   = $rgb         & 0xFF;
                        $lum = 0.299 * $r + 0.587 * $g + 0.114 * $b;
                        if ($lum < 150) {
                            $offset = $y * $widthBytes + ($x >> 3);
                            $rasterBytes[$offset] = chr(ord($rasterBytes[$offset]) | (0x80 >> ($x & 7)));
                        }
                    }
                }
                imagedestroy($thumb);

                $xL = $widthBytes & 0xFF;
                $xH = ($widthBytes >> 8) & 0xFF;
                $yL = $h & 0xFF;
                $yH = ($h >> 8) & 0xFF;

                // mode \x00 = normal size (clearer than double-width stretch)
                $data .= $GS . 'v0' . chr(0) . chr($xL) . chr($xH) . chr($yL) . chr($yH) . $rasterBytes;
                $data .= $LF . $LF;
            }
        }

        // ── Left align + receipt text ─────────────────────────────────
        $data .= $ESC . 'a' . "\x00";

        $data .= $ESC . 'E' . "\x01" . 'SHEZA LAUNDRY SOLO' . $LF . $ESC . 'E' . "\x00";
        $data .= 'Jl. Pucangsawit RT 03 RW 03' . $LF;
        $data .= 'Kec. Jebres, Surakarta' . $LF;
        $data .= 'Tel: +62 8820-0933-4660' . $LF;

        $data .= $sep . $LF;
        $data .= $cols2('Order ID :', $order->order_number) . $LF;
        $data .= $cols2('Tanggal  :', $order->created_at->translatedFormat('d F Y H:i')) . $LF;
        $data .= $cols2('Kasir    :', $order->user?->name ?? 'Admin') . $LF;
        $data .= $cols2('Pelanggan:', strtoupper($order->member?->name ?? 'TAMU')) . $LF;

        $data .= $sep . $LF;
        $data .= $ESC . 'E' . "\x01";
        $data .= $rpad('ITEM', 17) . $rpad('QTY', 7) . $lpad('SUBTOTAL', 8) . $LF;
        $data .= $ESC . 'E' . "\x00";

        foreach ($order->items as $item) {
            $qty = $item->service_type === 'kiloan'
                ? ($item->weight ? $item->weight . 'kg' : '?kg')
                : intval($item->quantity) . 'x';
            $sub = ($item->service_type === 'kiloan' && ! $item->weight)
                ? 'TBD'
                : number_format($item->subtotal, 0, ',', '.');
            $data .= $rpad($item->service_name, 17) . $rpad($qty, 7) . $lpad($sub, 8) . $LF;
        }

        $data .= $sep . $LF;
        $data .= $ESC . 'E' . "\x01";
        $data .= $cols2('TOTAL    :', 'Rp ' . number_format($order->total, 0, ',', '.')) . $LF;
        $data .= $ESC . 'E' . "\x00";

        if ($order->payment_status === 'lunas') {
            $data .= $sep . $LF;
            $data .= $cols2(
                'DIBAYAR (' . strtoupper($order->payment_method ?? 'TUNAI') . '):',
                'Rp ' . number_format($order->paid_amount ?? $order->total, 0, ',', '.')
            ) . $LF;
        }

        $data .= $sep . $LF;

        $finishAt = $order->ready_at
            ? $order->ready_at->translatedFormat('d F Y')
            : $order->created_at->addDays($order->is_express ? 1 : 3)->translatedFormat('d F Y');
        $data .= $cols2('Tgl Selesai:', $finishAt) . $LF;
        $data .= $sep . $LF;

        $data .= $ESC . 'a' . "\x01";
        $data .= $ESC . 'E' . "\x01" . 'TERIMA KASIH!' . $LF . $ESC . 'E' . "\x00";
        $data .= $center('Tidak Menerima Laundry Pakaian Dalam') . $LF;
        $data .= $center('Menerima Laundry Alat Gunung') . $LF;
        $data .= $center('IG : @sheza_laundrysolo') . $LF;
        $data .= $center('IG : @krabatadventure') . $LF;
        $data .= $sep . $LF;
        $data .= $center('Dicetak: ' . now()->translatedFormat('d F Y H:i')) . $LF;
        // Cut paper
        $data .= $GS . 'V' . "\x41" . "\x03";

        return $data;
    }
}
