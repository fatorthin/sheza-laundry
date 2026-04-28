<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private ?string $basicAuth;
    private ?string $deviceId;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.whatsapp.url', ''), '/');
        $this->basicAuth = config('services.whatsapp.basic_auth');
        $this->deviceId  = config('services.whatsapp.device_id');
    }

    /**
     * Normalise an Indonesian phone number to WA JID format.
     * e.g. "08123456789" / "+62 812-3456-789" → "628123456789@s.whatsapp.net"
     */
    public function normalisePhone(string $phone): ?string
    {
        // Strip all non-numeric characters
        $digits = preg_replace('/\D/', '', $phone);

        if (!$digits) {
            return null;
        }

        // Convert leading 0 → 62 (Indonesia)
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        // Accept numbers that already start with a country code (assume 62 if not)
        if (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits . '@s.whatsapp.net';
    }

    /**
     * Send a plain-text WhatsApp message.
     *
     * @return bool  true on success, false on failure
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $jid = $this->normalisePhone($phone);

        if (!$jid) {
            Log::warning('WhatsAppService: invalid phone number', ['phone' => $phone]);
            return false;
        }

        if (!$this->baseUrl) {
            Log::warning('WhatsAppService: WHATSAPP_GATEWAY_URL is not configured.');
            return false;
        }

        try {
            $headers = ['Content-Type' => 'application/json'];

            if ($this->deviceId) {
                $headers['X-Device-Id'] = $this->deviceId;
            }

            $request = Http::timeout(8)
                ->withHeaders($headers);

            if ($this->basicAuth) {
                [$user, $pass] = explode(':', $this->basicAuth, 2);
                $request = $request->withBasicAuth($user, $pass);
            }

            Log::info('WhatsAppService: sending message', [
                'jid'     => $jid,
                'gateway' => $this->baseUrl,
            ]);

            $response = $request->post("{$this->baseUrl}/send/message", [
                'phone'   => $jid,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsAppService: message sent successfully', ['jid' => $jid]);
                return true;
            }

            Log::warning('WhatsAppService: gateway returned non-2xx', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsAppService: request failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // -----------------------------------------------------------------------
    // Convenience helpers for order-related notifications
    // -----------------------------------------------------------------------

    public function notifyOrderStatus(string $phone, string $orderNumber, string $statusLabel): bool
    {
        $message = "Halo, pesanan laundry Anda *{$orderNumber}* telah diperbarui menjadi: *{$statusLabel}*.\n\nTerima kasih telah menggunakan layanan kami. 🙏";

        return $this->sendMessage($phone, $message);
    }

    public function notifyReadyForPickup(string $phone, string $orderNumber, string $total): bool
    {
        $totalFormatted = 'Rp ' . number_format((float) $total, 0, ',', '.');

        $message = "Halo, pesanan laundry Anda *{$orderNumber}* sudah *siap diambil*! 🎉\n\nTotal tagihan: *{$totalFormatted}*\n\nSilakan datang ke toko kami. Terima kasih! 🙏";

        return $this->sendMessage($phone, $message);
    }

    public function notifyOrderCompleted(string $phone, string $orderNumber, string $paymentMethod): bool
    {
        $method = ucfirst($paymentMethod);

        $message = "Halo, pembayaran pesanan *{$orderNumber}* telah kami terima via *{$method}*. Pesanan Anda sudah selesai. ✅\n\nTerima kasih telah mempercayakan laundry Anda kepada kami! 🙏";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Send an image via WhatsApp (multipart/form-data).
     *
     * @param  string  $imageBase64  Base64-encoded PNG/JPEG data
     */
    public function sendImage(string $phone, string $imageBase64, string $caption = ''): bool
    {
        $jid = $this->normalisePhone($phone);

        if (!$jid || !$this->baseUrl) {
            return false;
        }

        try {
            $headers = [];
            if ($this->deviceId) {
                $headers['X-Device-Id'] = $this->deviceId;
            }

            $request = Http::timeout(30)->withHeaders($headers);

            if ($this->basicAuth) {
                [$user, $pass] = explode(':', $this->basicAuth, 2);
                $request = $request->withBasicAuth($user, $pass);
            }

            $response = $request
                ->attach('image', base64_decode($imageBase64), 'invoice.png', ['Content-Type' => 'image/png'])
                ->post("{$this->baseUrl}/send/image", [
                    'phone'   => $jid,
                    'caption' => $caption,
                ]);

            if ($response->successful()) {
                Log::info('WhatsAppService: image sent successfully', ['jid' => $jid]);
                return true;
            }

            Log::warning('WhatsAppService: image send failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsAppService: image send error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Notify customer that order is ready for pickup AND attach the invoice image.
     */
    public function notifyReadyForPickupWithInvoice(string $phone, Order $order): bool
    {
        $total = 'Rp ' . number_format((float) $order->total, 0, ',', '.');

        // 1. Text notification first
        $message = "Halo, pesanan laundry Anda *{$order->order_number}* sudah *siap diambil*! 🎉\n\n"
            . "Total tagihan: *{$total}*\n\n"
            . "Silakan datang ke toko kami. Nota/invoice terlampir. Terima kasih! 🙏";

        $this->sendMessage($phone, $message);

        // 2. Invoice image
        try {
            /** @var InvoiceImageService $invoiceService */
            $invoiceService = app(InvoiceImageService::class);
            $base64         = $invoiceService->generateBase64($order);

            return $this->sendImage($phone, $base64, "Invoice – {$order->order_number}");
        } catch (\Throwable $e) {
            Log::error('WhatsAppService: invoice image generation failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }
}
