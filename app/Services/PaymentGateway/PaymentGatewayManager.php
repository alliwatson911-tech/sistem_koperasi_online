<?php

namespace App\Services\PaymentGateway;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use InvalidArgumentException;

class PaymentGatewayManager implements PaymentGatewayInterface
{
    protected array $drivers = [];

    public function __construct(
        protected FakePaymentGateway $fakeDriver,
        protected MidtransPaymentGateway $midtransDriver
    ) {
        $this->drivers['fake'] = $fakeDriver;
        $this->drivers['midtrans'] = $midtransDriver;
    }

    /**
     * Dapatkan driver yang diinginkan atau driver default aktif.
     */
    public function driver(?string $name = null): PaymentGatewayInterface
    {
        $driverName = $name ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$driverName])) {
            throw new InvalidArgumentException("Driver payment gateway [{$driverName}] tidak didukung.");
        }

        return $this->drivers[$driverName];
    }

    /**
     * Tentukan driver default dari Setting DB atau konfigurasi .env.
     */
    public function getDefaultDriver(): string
    {
        // Prioritaskan setting dinamis dari admin koperasi di database
        $dbDriver = Setting::get('payment_gateway_driver');
        if (!empty($dbDriver) && isset($this->drivers[$dbDriver])) {
            return $dbDriver;
        }

        return config('services.payment_gateway.driver', 'fake');
    }

    public function getDriverName(): string
    {
        return $this->driver()->getDriverName();
    }

    public function createCharge(array $params): array
    {
        return $this->driver()->createCharge($params);
    }

    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        return $this->driver()->verifyWebhook($payload, $signature);
    }

    public function parseWebhook(array $payload): array
    {
        return $this->driver()->parseWebhook($payload);
    }

    public function checkStatus(string $orderId): array
    {
        return $this->driver()->checkStatus($orderId);
    }

    public function cancelPayment(string $orderId): bool
    {
        return $this->driver()->cancelPayment($orderId);
    }
}

