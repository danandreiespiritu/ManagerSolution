<?php
namespace App\Repositories\Payment;

use App\Models\Payment as PaymentModel;

interface IPaymentRepository
{
    public function create(int $userId, array $data): PaymentModel;

    public function allocate(int $userId, int $paymentId, int $invoiceId, float $amount): bool;
}
