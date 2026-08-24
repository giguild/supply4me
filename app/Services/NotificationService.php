<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\Core\User;
use App\Mail\GenericNotificationMail;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create one notification and assign it to multiple recipients.
     */
    public function create(array $params): ?Notification
    {
        $recipients = $params['recipients'] ?? [];

        if (empty($recipients)) {
            return null;
        }

        $notification = Notification::create([
            'type' => $params['type'] ?? 'general',
            'title' => $params['title'] ?? 'Notification',
            'message' => $params['message'] ?? '',
            'action_url' => $params['action_url'] ?? null,
            'action_label' => $params['action_label'] ?? null,
            'icon' => $params['icon'] ?? 'bell',
            'metadata' => $params['metadata'] ?? [],
        ]);

        foreach ($recipients as $user) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ]);

            if (!empty($params['email']) && $user->email) {
                $this->sendEmail($user, [
                    'title' => $params['title'] ?? 'Notification',
                    'message' => $params['message'] ?? '',
                    'type' => $params['type'] ?? 'general',
                    'action_url' => $params['action_url'] ?? null,
                    'action_label' => $params['action_label'] ?? null,
                ]);
            }
        }

        return $notification;
    }

    /**
     * Get all users with given roles as recipients.
     */
    private function getRecipientsByRoles(array $roleNames): \Illuminate\Support\Collection
    {
        return User::whereHas('roles', fn ($q) => $q->whereIn('name', $roleNames))->get();
    }

    /**
     * Send email notification.
     */
    private function sendEmail(User $user, array $data): void
    {
        try {
            Mail::to($user->email)->queue(new GenericNotificationMail($data));
        } catch (\Exception $e) {
            \Log::error('Failed to send notification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ─── Convenience Methods ────────────────────────────

    public function orderPlaced($order): void
    {
        $customer = $order->customer;
        $salesRep = $customer->assignedTo;

        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);
        if ($salesRep) {
            $recipients = $recipients->push($salesRep)->unique('id');
        }

        $this->create([
            'type' => 'order_placed',
            'title' => 'New Order Received',
            'message' => "Order #{$order->order_number} has been placed by {$customer->name}.",
            'action_url' => route('orders.show', $order->id),
            'action_label' => 'View Order',
            'icon' => 'shopping-cart',
            'metadata' => ['order_id' => $order->id, 'customer_id' => $customer->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function orderConfirmed($order): void
    {
        $customer = $order->customer;
        $salesRep = $customer->assignedTo;

        $recipients = collect([$customer]);
        if ($salesRep) {
            $recipients = $recipients->push($salesRep);
        }

        $this->create([
            'type' => 'order_confirmed',
            'title' => 'Order Confirmed',
            'message' => "Order #{$order->order_number} has been confirmed.",
            'action_url' => route('orders.show', $order->id),
            'action_label' => 'View Order',
            'icon' => 'check-circle',
            'metadata' => ['order_id' => $order->id],
            'recipients' => $recipients,
        ]);
    }

    public function orderCancelled($order): void
    {
        $customer = $order->customer;
        $salesRep = $customer->assignedTo;

        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);
        if ($salesRep) {
            $recipients = $recipients->push($salesRep)->unique('id');
        }

        $this->create([
            'type' => 'order_cancelled',
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->order_number} has been cancelled.",
            'action_url' => route('orders.show', $order->id),
            'action_label' => 'View Order',
            'icon' => 'x-circle',
            'metadata' => ['order_id' => $order->id],
            'recipients' => $recipients,
        ]);
    }

    public function paymentReceived($payment): void
    {
        $customer = $payment->customer;
        $salesRep = $customer->assignedTo;

        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);
        if ($salesRep) {
            $recipients = $recipients->push($salesRep)->unique('id');
        }

        $this->create([
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => "Payment of ₦" . number_format($payment->amount, 2) . " received from {$customer->name}.",
            'action_url' => route('payments.show', $payment->id),
            'action_label' => 'View Payment',
            'icon' => 'credit-card',
            'metadata' => ['payment_id' => $payment->id, 'customer_id' => $customer->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function invoiceCreated($invoice): void
    {
        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);

        $this->create([
            'type' => 'invoice_created',
            'title' => 'New Invoice',
            'message' => "Invoice #{$invoice->invoice_number} created for {$invoice->customer->name}.",
            'action_url' => route('invoices.show', $invoice->id),
            'action_label' => 'View Invoice',
            'icon' => 'file-text',
            'metadata' => ['invoice_id' => $invoice->id],
            'recipients' => $recipients,
        ]);
    }

    public function invoiceOverdue($invoice): void
    {
        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);

        $this->create([
            'type' => 'invoice_overdue',
            'title' => 'Invoice Overdue',
            'message' => "Invoice #{$invoice->invoice_number} is overdue. Amount: ₦" . number_format($invoice->due_amount, 2),
            'action_url' => route('invoices.show', $invoice->id),
            'action_label' => 'View Invoice',
            'icon' => 'alert-triangle',
            'metadata' => ['invoice_id' => $invoice->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function deliveryUpdate($delivery, string $status): void
    {
        $order = $delivery->order;
        $customer = $order->customer ?? null;

        $statusMessages = [
            'in_transit' => 'is now in transit',
            'delivered' => 'has been delivered',
            'failed' => 'delivery attempt failed',
        ];

        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);
        if ($customer) {
            $recipients = $recipients->push($customer)->unique('id');
        }

        $this->create([
            'type' => 'delivery_update',
            'title' => 'Delivery Update',
            'message' => "Delivery for order #{$order->order_number} " . ($statusMessages[$status] ?? "status: {$status}") . ".",
            'action_url' => route('deliveries.show', $delivery->id),
            'action_label' => 'View Delivery',
            'icon' => 'truck',
            'metadata' => ['delivery_id' => $delivery->id, 'status' => $status],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function lowStock($product, int $currentQty): void
    {
        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);

        $this->create([
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "{$product->name} is running low. Current stock: {$currentQty}.",
            'action_url' => route('products.show', $product->id),
            'action_label' => 'View Product',
            'icon' => 'alert-triangle',
            'metadata' => ['product_id' => $product->id, 'current_qty' => $currentQty],
            'recipients' => $recipients,
        ]);
    }

    public function grnCompleted($grn): void
    {
        $recipients = $this->getRecipientsByRoles(['admin', 'super_admin']);

        $this->create([
            'type' => 'grn_completed',
            'title' => 'Goods Received',
            'message' => "GRN #{$grn->grn_number} has been completed.",
            'action_url' => route('grns.show', $grn->id),
            'action_label' => 'View GRN',
            'icon' => 'package',
            'metadata' => ['grn_id' => $grn->id],
            'recipients' => $recipients,
        ]);
    }
}
