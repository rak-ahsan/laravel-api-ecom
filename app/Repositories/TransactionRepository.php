<?php

namespace App\Repositories;

use Exception;
use App\Models\Transaction;

class TransactionRepository
{
    public function createTransaction($orderId, $payableAmount, $paymentGatewayId, $type = null)
    {
        try {
            $transaction = new Transaction();

            $transaction->order_id           = $orderId;
            $transaction->payable_amount     = $payableAmount;
            $transaction->payment_gateway_id = $paymentGatewayId;
            $transaction->type               = $type;
            $transaction->save();

            return $transaction;
        } catch (Exception $exception) {
            throw $exception;
        }

    }
}
