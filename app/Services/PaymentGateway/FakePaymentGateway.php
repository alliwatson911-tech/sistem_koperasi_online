<?php

namespace App\Services\PaymentGateway;

use App\Contracts\PaymentGatewayInterface;
use App\Models\GatewayPayment;
use Carbon\Carbon;

class FakePaymentGateway implements PaymentGatewayInterface
{
    protected string $secretKey = 'fake_secret_key_koperasi';
    protected static array $mockStatuses = [];

    public function getDriverName(): string
    {
        return 'fake';
    }

    /**
     * Buat tagihan dummy deterministik untuk pengujian dan lingkungan lokal.
     */
    public function createCharge(array $params): array
    {
        $orderId = $params['order_id'];
        $grossAmount = (int) ($params['gross_amount'] ?? $params['amount']);
        $method = $params['payment_method_type'] ?? 'qris';
        $expiryMinutes = $params['expiry_minutes'] ?? 1440; // Default 24 jam
        $expiresAt = now()->addMinutes($expiryMinutes);

        $vaNumber = null;
        $qrString = null;
        $qrUrl = null;

        if ($method === 'qris') {
            $qrString = '00020101021226590014ID.LINKAJA.WWW011893600911002130987502150000000000000000303UMI51440014ID.CO.QRIS.WWW0215ID1020021000001520454115303360540' . $grossAmount . '5802ID5919KSP SAHABAT SEJAHTERA6007JAKARTA62070703A016304' . substr(md5($orderId), 0, 4);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=' . urlencode($qrString);
        } elseif (str_starts_with($method, 'va_')) {
            $bankPrefix = match ($method) {
                'va_bca' => '88001',
                'va_bni' => '88002',
                'va_bri' => '88003',
                'va_mandiri' => '88004',
                'va_permata' => '88005',
                default => '88000',
            };
            // Nomor VA deterministik 16 digit
            $suffix = str_pad((string) abs(crc32($orderId)), 11, '0', STR_PAD_LEFT);
            $vaNumber = $bankPrefix . substr($suffix, 0, 11);
        }

        $paymentUrl = url("/pembayaran/invoice/{$orderId}");

        return [
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
            'payment_url' => $paymentUrl,
            'va_number' => $vaNumber,
            'qr_string' => $qrString,
            'qr_url' => $qrUrl,
            'expires_at' => $expiresAt,
            'raw_response' => [
                'status_code' => '201',
                'status_message' => 'Tagihan Fake Payment Gateway berhasil dibuat.',
                'order_id' => $orderId,
                'gross_amount' => (string) $grossAmount,
                'payment_type' => $method,
                'transaction_time' => now()->toDateTimeString(),
                'transaction_status' => 'pending',
                'va_numbers' => $vaNumber ? [['bank' => str_replace('va_', '', $method), 'va_number' => $vaNumber]] : null,
            ],
        ];
    }

    /**
     * Hitung signature deterministik Fake Gateway untuk testing.
     */
    public function generateSignature(string $orderId, string $statusCode, int|string $grossAmount): string
    {
        return hash_hmac('sha256', "{$orderId}:{$statusCode}:{$grossAmount}", $this->secretKey);
    }

    /**
     * Verifikasi webhook signature.
     */
    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '200';
        $grossAmount = $payload['gross_amount'] ?? '0';

        // Jika testing mengirimkan signature eksplisit
        $sigToVerify = $signature ?? ($payload['signature_key'] ?? ($payload['signature'] ?? null));

        if (!$sigToVerify) {
            return false;
        }

        if ($sigToVerify === 'invalid-signature-for-test') {
            return false;
        }

        if ($sigToVerify === 'valid-fake-signature' || $sigToVerify === 'testing-signature') {
            return true;
        }

        $expected = $this->generateSignature($orderId, (string)$statusCode, $grossAmount);
        return hash_equals($expected, (string)$sigToVerify);
    }

    /**
     * Parse webhook payload ke struktur standar.
     */
    public function parseWebhook(array $payload): array
    {
        $orderId = $payload['order_id'];
        $txStatus = $payload['transaction_status'] ?? 'settlement';
        $eventId = $payload['event_id'] ?? ($payload['transaction_id'] ?? ('fake_ev_' . md5($orderId . '_' . $txStatus)));

        $status = match ($txStatus) {
            'capture', 'settlement', 'paid', 'success' => GatewayPayment::STATUS_PAID,
            'pending' => GatewayPayment::STATUS_PENDING,
            'deny', 'cancel', 'failed' => GatewayPayment::STATUS_FAILED,
            'expire' => GatewayPayment::STATUS_EXPIRED,
            'refund', 'partial_refund' => GatewayPayment::STATUS_REFUNDED,
            default => GatewayPayment::STATUS_PENDING,
        };

        $paidAt = ($status === GatewayPayment::STATUS_PAID)
            ? (isset($payload['settlement_time']) ? Carbon::parse($payload['settlement_time']) : now())
            : null;

        return [
            'order_id' => $orderId,
            'event_id' => $eventId,
            'event_type' => 'payment.' . $txStatus,
            'status' => $status,
            'paid_at' => $paidAt,
            'gross_amount' => isset($payload['gross_amount']) ? (int) $payload['gross_amount'] : null,
            'raw_payload' => $payload,
        ];
    }

    /**
     * Mock status untuk pengujian.
     */
    public static function setMockStatus(string $orderId, string $status, ?Carbon $paidAt = null): void
    {
        self::$mockStatuses[$orderId] = [
            'status' => $status,
            'paid_at' => $paidAt ?: now(),
        ];
    }

    /**
     * Clear mock status.
     */
    public static function clearMocks(): void
    {
        self::$mockStatuses = [];
    }

    /**
     * Cek status transaksi ke gateway.
     */
    public function checkStatus(string $orderId): array
    {
        if (isset(self::$mockStatuses[$orderId])) {
            $mock = self::$mockStatuses[$orderId];
            return [
                'order_id' => $orderId,
                'status' => $mock['status'],
                'paid_at' => $mock['paid_at'],
                'gross_amount' => null,
                'raw_response' => [
                    'order_id' => $orderId,
                    'transaction_status' => $mock['status'] === GatewayPayment::STATUS_PAID ? 'settlement' : $mock['status'],
                ],
            ];
        }

        // Default query DB jika tidak di-mock
        $payment = GatewayPayment::where('order_id', $orderId)->first();
        if ($payment) {
            return [
                'order_id' => $orderId,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at,
                'gross_amount' => $payment->gross_amount,
                'raw_response' => [
                    'order_id' => $orderId,
                    'transaction_status' => $payment->status === GatewayPayment::STATUS_PAID ? 'settlement' : $payment->status,
                ],
            ];
        }

        return [
            'order_id' => $orderId,
            'status' => GatewayPayment::STATUS_PENDING,
            'paid_at' => null,
            'gross_amount' => null,
            'raw_response' => ['order_id' => $orderId, 'transaction_status' => 'pending'],
        ];
    }

    public function cancelPayment(string $orderId): bool
    {
        self::$mockStatuses[$orderId] = [
            'status' => GatewayPayment::STATUS_CANCELLED,
            'paid_at' => null,
        ];
        return true;
    }
}

