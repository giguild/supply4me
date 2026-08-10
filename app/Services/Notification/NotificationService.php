<?php

namespace App\Services\Notification;

use App\Models\Delivery\Delivery;
use App\Models\Invoicing\Invoice;
use App\Models\Inventory\StockItem;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;

class NotificationService
{
    public function __construct(
        private readonly EmailService $emailService,
    ) {}

    /**
     * Send order confirmation email to customer.
     */
    public function sendOrderConfirmation(Order $order): void
    {
        $customer = $order->customer;

        if (!$customer || !$customer->email) {
            return;
        }

        $this->emailService->send(
            to: $customer->email,
            subject: "Order Confirmation - {$order->order_number}",
            view: 'emails.orders.confirmation',
            data: [
                'order' => $order,
                'customer' => $customer,
                'items' => $order->items,
            ]
        );
    }

    /**
     * Send payment receipt email.
     */
    public function sendPaymentReceipt(Payment $payment): void
    {
        $customer = $payment->customer;

        if (!$customer || !$customer->email) {
            return;
        }

        $this->emailService->send(
            to: $customer->email,
            subject: "Payment Receipt - {$payment->payment_number}",
            view: 'emails.payments.receipt',
            data: [
                'payment' => $payment,
                'customer' => $customer,
            ]
        );
    }

    /**
     * Send invoice email to customer.
     */
    public function sendInvoiceEmail(Invoice $invoice): void
    {
        $customer = $invoice->customer;

        if (!$customer || !$customer->email) {
            return;
        }

        $this->emailService->send(
            to: $customer->email,
            subject: "Invoice - {$invoice->invoice_number}",
            view: 'emails.invoices.send',
            data: [
                'invoice' => $invoice,
                'customer' => $customer,
                'items' => $invoice->items,
            ]
        );
    }

    /**
     * Send delivery notification to customer.
     */
    public function sendDeliveryNotification(Delivery $delivery): void
    {
        $customer = $delivery->customer;

        if (!$customer || !$customer->email) {
            return;
        }

        $this->emailService->send(
            to: $customer->email,
            subject: "Delivery Update - {$delivery->delivery_number}",
            view: 'emails.deliveries.notification',
            data: [
                'delivery' => $delivery,
                'customer' => $customer,
            ]
        );
    }

    /**
     * Send low stock alert to warehouse manager.
     */
    public function sendLowStockAlert(StockItem $stockItem): void
    {
        $product = $stockItem->product;
        $warehouse = $stockItem->warehouse;

        if (!$product || !$warehouse) {
            return;
        }

        $available = (float) $stockItem->quantity_on_hand - (float) $stockItem->quantity_reserved;

        $this->emailService->queue(
            to: config('mail.manager_email', config('mail.from.address')),
            subject: "Low Stock Alert - {$product->name}",
            view: 'emails.inventory.low_stock',
            data: [
                'stockItem' => $stockItem,
                'product' => $product,
                'warehouse' => $warehouse,
                'available_quantity' => $available,
                'reorder_level' => (float) $stockItem->reorder_level,
            ]
        );
    }
}
