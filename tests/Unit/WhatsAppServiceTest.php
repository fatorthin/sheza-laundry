<?php

namespace Tests\Unit;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    // -----------------------------------------------------------------------
    // normalisePhone
    // -----------------------------------------------------------------------

    public function test_normalise_phone_with_leading_zero(): void
    {
        $svc = new WhatsAppService();
        $this->assertSame('628123456789@s.whatsapp.net', $svc->normalisePhone('08123456789'));
    }

    public function test_normalise_phone_already_62(): void
    {
        $svc = new WhatsAppService();
        $this->assertSame('628123456789@s.whatsapp.net', $svc->normalisePhone('628123456789'));
    }

    public function test_normalise_phone_with_plus_and_spaces(): void
    {
        $svc = new WhatsAppService();
        $this->assertSame('628123456789@s.whatsapp.net', $svc->normalisePhone('+62 812-3456-789'));
    }

    public function test_normalise_phone_short_number_prepends_62(): void
    {
        $svc = new WhatsAppService();
        // Numbers not starting with 0 or 62 get 62 prepended
        $this->assertSame('628561234567@s.whatsapp.net', $svc->normalisePhone('8561234567'));
    }

    public function test_normalise_phone_returns_null_for_empty(): void
    {
        $svc = new WhatsAppService();
        $this->assertNull($svc->normalisePhone(''));
    }

    public function test_normalise_phone_returns_null_for_non_digits(): void
    {
        $svc = new WhatsAppService();
        $this->assertNull($svc->normalisePhone('abc-xyz'));
    }

    // -----------------------------------------------------------------------
    // sendMessage — gateway not configured
    // -----------------------------------------------------------------------

    public function test_send_message_returns_false_when_url_not_configured(): void
    {
        config(['services.whatsapp.url' => '']);

        Log::shouldReceive('warning')->once()->with('WhatsAppService: WHATSAPP_GATEWAY_URL is not configured.');

        $svc = new WhatsAppService();
        $result = $svc->sendMessage('08123456789', 'Test pesan');

        $this->assertFalse($result);
    }

    // -----------------------------------------------------------------------
    // sendMessage — HTTP fakes
    // -----------------------------------------------------------------------

    public function test_send_message_returns_true_on_success(): void
    {
        config(['services.whatsapp.url' => 'https://wagateway.surakana.my.id']);

        Http::fake([
            'wagateway.surakana.my.id/send/message' => Http::response(['message' => 'ok'], 200),
        ]);

        $svc = new WhatsAppService();
        $result = $svc->sendMessage('08123456789', 'Test pesan');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/send/message')
                && $request['phone'] === '628123456789@s.whatsapp.net'
                && $request['message'] === 'Test pesan';
        });
    }

    public function test_send_message_returns_false_on_non_2xx(): void
    {
        config(['services.whatsapp.url' => 'https://wagateway.surakana.my.id']);

        Http::fake([
            'wagateway.surakana.my.id/send/message' => Http::response(['error' => 'not connected'], 503),
        ]);

        $svc = new WhatsAppService();
        $result = $svc->sendMessage('08123456789', 'Test pesan');

        $this->assertFalse($result);
    }

    public function test_send_message_returns_false_on_network_error(): void
    {
        config(['services.whatsapp.url' => 'https://wagateway.surakana.my.id']);

        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        });

        $svc = new WhatsAppService();
        $result = $svc->sendMessage('08123456789', 'Test pesan');

        $this->assertFalse($result);
    }

    public function test_send_message_uses_basic_auth_when_configured(): void
    {
        config([
            'services.whatsapp.url'        => 'https://wagateway.surakana.my.id',
            'services.whatsapp.basic_auth' => 'admin:secret',
        ]);

        Http::fake([
            'wagateway.surakana.my.id/send/message' => Http::response(['message' => 'ok'], 200),
        ]);

        $svc = new WhatsAppService();
        $result = $svc->sendMessage('08123456789', 'Test pesan');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request->header('Authorization')[0] ?? '', 'Basic ');
        });
    }

    // -----------------------------------------------------------------------
    // Notification helpers — verify message content
    // -----------------------------------------------------------------------

    public function test_notify_order_status_sends_correct_message(): void
    {
        config(['services.whatsapp.url' => 'https://wagateway.surakana.my.id']);

        Http::fake([
            'wagateway.surakana.my.id/send/message' => Http::response(['message' => 'ok'], 200),
        ]);

        $svc = new WhatsAppService();
        $result = $svc->notifyOrderStatus('08123456789', 'ORD-001', 'Dicuci');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request['message'], 'ORD-001')
                && str_contains($request['message'], 'Dicuci');
        });
    }

    public function test_notify_ready_for_pickup_formats_currency_correctly(): void
    {
        config(['services.whatsapp.url' => 'https://wagateway.surakana.my.id']);

        Http::fake([
            'wagateway.surakana.my.id/send/message' => Http::response(['message' => 'ok'], 200),
        ]);

        $svc = new WhatsAppService();
        $result = $svc->notifyReadyForPickup('08123456789', 'ORD-002', '35000');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request['message'], 'ORD-002')
                && str_contains($request['message'], 'Rp 35.000')
                && str_contains($request['message'], 'siap diambil');
        });
    }

    public function test_notify_order_completed_includes_payment_method(): void
    {
        config(['services.whatsapp.url' => 'https://wagateway.surakana.my.id']);

        Http::fake([
            'wagateway.surakana.my.id/send/message' => Http::response(['message' => 'ok'], 200),
        ]);

        $svc = new WhatsAppService();
        $result = $svc->notifyOrderCompleted('08123456789', 'ORD-003', 'transfer');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request['message'], 'ORD-003')
                && str_contains($request['message'], 'Transfer');
        });
    }
}
