<?php

namespace App\Services\Invoice;

use App\Models\Invoicing\Invoice;
use App\ValueObjects\Money;

class InvoiceCalculationService
{
    /**
     * Calculate invoice subtotal from all items.
     */
    public function calculateSubtotal(Invoice $invoice): Money
    {
        $subtotal = Money::zero($invoice->currency_code ?? 'USD');

        foreach ($invoice->items as $item) {
            $unitPrice = Money::from((float) $item->unit_price, $invoice->currency_code ?? 'USD');
            $lineTotal = $unitPrice->multiply((float) $item->quantity);

            if ($item->discount_percentage > 0) {
                $discountAmount = $lineTotal->multiply($item->discount_percentage / 100);
                $lineTotal = $lineTotal->subtract($discountAmount);
            }

            $subtotal = $subtotal->add($lineTotal);
        }

        return $subtotal;
    }

    /**
     * Calculate total tax for the invoice.
     */
    public function calculateTax(Invoice $invoice): Money
    {
        $totalTax = Money::zero($invoice->currency_code ?? 'USD');

        foreach ($invoice->items as $item) {
            $itemTax = Money::from((float) $item->tax_amount, $invoice->currency_code ?? 'USD');
            $totalTax = $totalTax->add($itemTax);
        }

        return $totalTax;
    }

    /**
     * Calculate the invoice grand total.
     */
    public function calculateTotal(Invoice $invoice): Money
    {
        $subtotal = $this->calculateSubtotal($invoice);
        $tax = $this->calculateTax($invoice);

        return $subtotal->add($tax);
    }

    /**
     * Get the outstanding amount due on the invoice.
     */
    public function getAmountDue(Invoice $invoice): Money
    {
        $total = Money::from((float) $invoice->total_amount, $invoice->currency_code ?? 'USD');
        $amountPaid = Money::from((float) $invoice->paid_amount, $invoice->currency_code ?? 'USD');

        return $total->subtract($amountPaid);
    }

    /**
     * Calculate and update all invoice totals.
     */
    public function calculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = $this->calculateSubtotal($invoice);
        $tax = $this->calculateTax($invoice);
        $total = $subtotal->add($tax);
        $amountPaid = Money::from((float) $invoice->paid_amount, $invoice->currency_code ?? 'USD');
        $balanceDue = $total->subtract($amountPaid);

        $invoice->update([
            'subtotal' => $subtotal->getAmount(),
            'tax_amount' => $tax->getAmount(),
            'total_amount' => $total->getAmount(),
            'due_amount' => max(0, $balanceDue->getAmount()),
        ]);
    }
}
