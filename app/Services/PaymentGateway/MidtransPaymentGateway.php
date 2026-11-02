<?php

namespace App\Services\PaymentGateway;

use App\Contracts\PaymentGatewayInterface;
use App\Models\GatewayPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransPaymentGateway implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected string $clientKey;
    protected string $merchantId;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-TESTKEY'));
        $this->clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-TESTKEY'));
        $this->merchantId = config('services.midtrans.merchant_id', env('MIDTRANS_MERCHANT_ID', ''));
        $this->isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
    }

    public function getDriverName(): string
    {
        return 'midtrans';
    }

    protected function getSnapBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    protected function getCoreBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Buat tagihan lewat Midtrans Snap / Core API.
     */
    public function createCharge(array $params): array
    {
        $orderId = $params['order_id'];
        $grossAmount = (int) ($params['gross_amount'] ?? $params['amount']);
        $method = $params['payment_method_type'] ?? 'qris';
        $expiryMinutes = $params['expiry_minutes'] ?? 1440;
        $expiresAt = now()->addMinutes($expiryMinutes);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $params['customer_name'] ?? 'Anggota Koperasi',
                'email' => $params['customer_email'] ?? 'anggota@koperasi.local',
                'phone' => $params['customer_phone'] ?? '081234567890',
            ],
            'item_details' => [
                [
                    'id' => $orderId,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => mb_strimwidth($params['description'] ?? 'Pembayaran KSP Sahabat Sejahtera', 0, 45, '...'),
                ],
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => $expiryMinutes,
            ],
        ];

        // Mapping channel spesifik ke Midtrans
        if ($method === 'qris') {
            $payload['enabled_payments'] = ['other_qris', 'gopay', 'shopeepay'];
        } elseif (str_starts_with($method, 'va_')) {
            $bank = str_replace('va_', '', $method);
            $payload['enabled_payments'] = match ($bank) {
                'bca' => ['bca_va'],
                'bni' => ['bni_va'],
                'bri' => ['bri_va'],
                'mandiri' => ['echannel'],
                'permata' => ['permata_va'],
                default => ['bank_transfer'],
            };
        }

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post($this->getSnapBaseUrl() . '/transactions', $payload);

            $data = $response->json() ?? [];

            $paymentUrl = $data['redirect_url'] ?? url("/pembayaran/invoice/{$orderId}");
            $token = $data['token'] ?? null;

            return [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'payment_url' => $paymentUrl,
                'va_number' => null, // Ditentukan saat customer memilih bank di Snap
                'qr_string' => null,
                'qr_url' => null,
                'expires_at' => $expiresAt,
                'raw_response' => array_merge($data, ['snap_token' => $token]),
            ];
        } catch (Exception $e) {
            Log::error("Midtrans charge failed for order {$orderId}: " . $e->getMessage());
            // Fallback graceful ke Invoice Page Internal
            return [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'payment_url' => url("/pembayaran/invoice/{$orderId}"),
                'va_number' => null,
                'qr_string' => null,
                'qr_url' => null,
                'expires_at' => $expiresAt,
                'raw_response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Verifikasi keaslian webhook signature Midtrans:
     * SHA512(order_id + status_code + gross_amount + ServerKey)
     */
    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $signature ?? ($payload['signature_key'] ?? '');

        if (empty($orderId) || empty($statusCode) || empty($signatureKey)) {
            return false;
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Parsing payload notifikasi Midtrans.
     */
    public function parseWebhook(array $payload): array
    {
        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';
        $eventId = $payload['transaction_id'] ?? ('midtrans_' . $orderId . '_' . $transactionStatus);

        $status = GatewayPayment::STATUS_PENDING;

        if ($transactionStatus === 'capture') {
            $status = ($fraudStatus === 'challenge') ? GatewayPayment::STATUS_PENDING : GatewayPayment::STATUS_PAID;
        } elseif ($transactionStatus === 'settlement') {
            $status = GatewayPayment::STATUS_PAID;
        } elseif (in_array($transactionStatus, ['cancel', 'deny'])) {
            $status = GatewayPayment::STATUS_FAILED;
        } elseif ($transactionStatus === 'expire') {
            $status = GatewayPayment::STATUS_EXPIRED;
        } elseif (in_array($transactionStatus, ['refund', 'partial_refund'])) {
            $status = GatewayPayment::STATUS_REFUNDED;
        }

        $paidAt = ($status === GatewayPayment::STATUS_PAID)
            ? (isset($payload['settlement_time']) ? Carbon::parse($payload['settlement_time']) : now())
            : null;

        return [
            'order_id' => $orderId,
            'event_id' => $eventId,
            'event_type' => 'payment.' . $transactionStatus,
            'status' => $status,
            'paid_at' => $paidAt,
            'gross_amount' => isset($payload['gross_amount']) ? (int) $payload['gross_amount'] : null,
            'raw_payload' => $payload,
        ];
    }

    /**
     * Cek status transaksi langsung ke Midtrans Core API.
     */
    public function checkStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(10)
                ->get($this->getCoreBaseUrl() . '/' . $orderId . '/status');

            $data = $response->json() ?? [];
            $parsed = $this->parseWebhook($data);

            return [
                'order_id' => $orderId,
                'status' => $parsed['status'],
                'paid_at' => $parsed['paid_at'],
                'gross_amount' => $parsed['gross_amount'],
                'raw_response' => $data,
            ];
        } catch (Exception $e) {
            Log::error("Midtrans checkStatus failed for order {$orderId}: " . $e->getMessage());
            return [
                'order_id' => $orderId,
                'status' => GatewayPayment::STATUS_PENDING,
                'paid_at' => null,
                'gross_amount' => null,
                'raw_response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Batalkan transaksi di Midtrans.
     */
    public function cancelPayment(string $orderId): bool
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(10)
                ->post($this->getCoreBaseUrl() . '/' . $orderId . '/cancel');

            return $response->successful();
        } catch (Exception $e) {
            Log::error("Midtrans cancelPayment failed for order {$orderId}: " . $e->getMessage());
            return false;
        }
    }
}

