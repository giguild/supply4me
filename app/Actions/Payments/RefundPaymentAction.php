<?php

namespace App\Actions\Payments;

use App\Events\Payments\PaymentRefunded;
use App\Models\Core\User;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\DB;

class RefundPaymentAction
{
    public function execute(Payment $payment, User $user, ?string $reason = null, ?float $refundAmount = null): Payment
    {
        if ($payment->status->value !== 'completed') {
            throw new \App\Exceptions\PaymentCannotBeRefundedException(
                'Only completed payments can be refunded.'
            );
        }

        $amount = $refundAmount ?? $payment->amount;

        if ($amount <= 0 || $amount > $payment->amount) {
            throw new \InvalidArgumentException(
                'Refund amount must be between 0.01 and ' . number_format($payment->amount, 2)
            );
        }

        return DB::transaction(function () use ($payment, $user, $reason, $amount) {
            $notes = $payment->notes ?? '';
            if ($reason) {
                $notes .= ($notes ? "\n" : '') . "Refund reason: {$reason}";
            }
            $notes .= ($notes ? "\n" : '') . "Refund amount: ₦" . number_format($amount, 2);

            $payment->update([
                'status' => 'refunded',
                'notes' => $notes,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'refund_amount' => $amount,
                    'refund_reason' => $reason,
                    'refunded_by' => $user->id,
                    'refunded_at' => now()->toISOString(),
                ]),
            ]);

            event(new PaymentRefunded($payment, $user));

            return $payment->fresh();
        });
    }
}
