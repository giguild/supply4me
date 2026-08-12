<?php

namespace App\Actions\Payments;

use App\Enums\Payments\PaymentStatus;
use App\Events\Payments\PaymentRejected;
use App\Models\Core\User;
use App\Models\Payments\Payment;

class RejectPaymentAction
{
    public function execute(Payment $payment, User $user, ?string $reason = null): Payment
    {
        if ($payment->status !== PaymentStatus::Pending) {
            throw new \App\Exceptions\PaymentCannotBeRejectedException(
                'Payment can only be rejected from pending status.'
            );
        }

        $payment->update([
            'status' => PaymentStatus::Rejected,
            'notes' => $reason ? ($payment->notes ? $payment->notes . "\n" . $reason : $reason) : $payment->notes,
        ]);

        event(new PaymentRejected($payment, $user, $reason));

        return $payment->fresh();
    }
}
