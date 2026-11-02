<?php

namespace App\Contracts;

use Carbon\Carbon;

interface PaymentGatewayInterface
{
    /**
     * Dapatkan nama pengenal driver gateway (misal 'fake', 'midtrans').
     */
    public function getDriverName(): string;

    /**
     * Buat transaksi / tagihan pembayaran baru di payment gateway.
     *
     * @param array $params [
     *     'order_id' => string,
     *     'amount' => int,
     *     'gross_amount' => int,
     *     'payment_method_type' => string, // 'qris', 'va_bca', 'va_bni', 'va_bri', 'va_mandiri', 'gopay', 'shopeepay'
     *     'customer_name' => string,
     *     'customer_email' => ?string,
     *     'customer_phone' => ?string,
     *     'description' => string,
     *     'expiry_minutes' => int,
     * ]
     * @return array [
     *     'order_id' => string,
     *     'gross_amount' => int,
     *     'payment_url' => ?string,
     *     'va_number' => ?string,
     *     'qr_string' => ?string,
     *     'qr_url' => ?string,
     *     'expires_at' => Carbon,
     *     'raw_response' => array,
     * ]
     */
    public function createCharge(array $params): array;

    /**
     * Verifikasi keabsahan signature payload webhook dari payment gateway.
     */
    public function verifyWebhook(array $payload, ?string $signature = null): bool;

    /**
     * Parse webhook payload ke struktur standar status sistem koperasi.
     *
     * @return array [
     *     'order_id' => string,
     *     'event_id' => string,
     *     'event_type' => string,
     *     'status' => string, // 'paid', 'pending', 'expired', 'failed', 'refunded'
     *     'paid_at' => ?Carbon,
     *     'gross_amount' => ?int,
     *     'raw_payload' => array,
     * ]
     */
    public function parseWebhook(array $payload): array;

    /**
     * Periksa status transaksi secara langsung ke gateway API.
     *
     * @return array [
     *     'order_id' => string,
     *     'status' => string, // 'paid', 'pending', 'expired', 'failed', 'refunded'
     *     'paid_at' => ?Carbon,
     *     'gross_amount' => ?int,
     *     'raw_response' => array,
     * ]
     */
    public function checkStatus(string $orderId): array;

    /**
     * Batalkan tagihan yang masih pending di gateway.
     */
    public function cancelPayment(string $orderId): bool;
}

