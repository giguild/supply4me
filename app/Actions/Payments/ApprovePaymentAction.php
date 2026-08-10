<?php

namespace App\Actions\Payments;

use App\Enums\Payments\PaymentStatus;
use App\Events\Payments\PaymentApproved;
use App\Models\Core\User;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\DB;

class ApprovePaymentAction
{
    public function execute(Payment $payment, User $user): Payment
    {
        if ($payment->status !== PaymentStatus::Pending) {
            throw new \App\Exceptions\PaymentCannotBeApprovedException(
                'Payment can only be approved from pending status.'
            );
        }

        return DB::transaction(function () use ($payment, $user) {
            $payment->update([
                'status' => PaymentStatus::Completed,
                'approved_by' => $user->id,
                'cleared_date' => now()->toDateString(),
            ]);

            event(new PaymentApproved($payment, $user));

            return $payment->fresh();
        });
    }
}
