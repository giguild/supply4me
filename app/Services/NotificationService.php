<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Models\Core\User;
use App\Mail\GenericNotificationMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
            if (!$user instanceof User || !$user->getKey()) {
                continue;
            }

            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $user->getKey(),
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
     * Send email notification.
     */
    private function sendEmail(User $user, array $data): void
    {
        try {
            Mail::to($user->email)->send(new GenericNotificationMail($data));
        } catch (\Exception $e) {
            \Log::error('Failed to send notification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Users who hold the given permission, scoped to the subject's company and
     * branch. Users not yet assigned to any branch still receive notifications
     * so new setups don't silently lose visibility.
     */
    private function recipientsForPermission(
        string $permission,
        ?string $companyId = null,
        ?string $branchId = null,
        array $extra = []
    ): Collection {
        $query = User::query()
            ->permission($permission);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->whereHas('branches', fn ($b) => $b->whereKey($branchId))
                  ->orWhereDoesntHave('branches');
            });
        }

        $recipients = $query->get();

        foreach ($extra as $user) {
            if ($user instanceof User && $user->getKey()) {
                $recipients = $recipients->push($user);
            }
        }

        return $recipients->unique('id')->values();
    }

    /**
     * Branch of the subject entity when it has one, otherwise null.
     */
    private function branchOf(?object $subject): ?string
    {
        if (!$subject || !isset($subject->branch_id)) {
            return null;
        }

        return is_null($subject->branch_id) ? null : $subject->branch_id;
    }

    /**
     * Send order confirmation email notification to relevant users.
     */
    public function orderPlaced($order): void
    {
        $salesRep = $order->customer?->assignedTo;

        $recipients = $this->recipientsForPermission(
            'order.view',
            $order->company_id ?? null,
            $this->branchOf($order),
            $salesRep ? [$salesRep] : []
        );

        $this->create([
            'type' => 'order_placed',
            'title' => 'New Order Received',
            'message' => "Order #{$order->order_number} has been placed by {$order->customer->name}.",
            'action_url' => route('orders.show', $order->id),
            'action_label' => 'View Order',
            'icon' => 'shopping-cart',
            'metadata' => ['order_id' => $order->id, 'customer_id' => $order->customer->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function orderConfirmed($order): void
    {
        $salesRep = $order->customer?->assignedTo;

        $recipients = $this->recipientsForPermission(
            'order.view',
            $order->company_id ?? null,
            $this->branchOf($order),
            $salesRep ? [$salesRep] : []
        );

        $this->create([
            'type' => 'order_confirmed',
            'title' => 'Order Confirmed',
            'message' => "Order #{$order->order_number} has been confirmed.",
            'action_url' => route('orders.show', $order->id),
            'action_label' => 'View Order',
            'icon' => 'check-circle',
            'metadata' => ['order_id' => $order->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function orderCancelled($order): void
    {
        $salesRep = $order->customer?->assignedTo;

        $recipients = $this->recipientsForPermission(
            'order.view',
            $order->company_id ?? null,
            $this->branchOf($order),
            $salesRep ? [$salesRep] : []
        );

        $this->create([
            'type' => 'order_cancelled',
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->order_number} has been cancelled.",
            'action_url' => route('orders.show', $order->id),
            'action_label' => 'View Order',
            'icon' => 'x-circle',
            'metadata' => ['order_id' => $order->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function paymentReceived($payment): void
    {
        $recipients = $this->recipientsForPermission(
            'payment.view',
            $payment->company_id ?? null,
            $this->branchOf($payment)
        );

        $this->create([
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => 'Payment of ₦' . number_format($payment->amount, 2) . ' received from ' . ($payment->customer->name ?? 'customer') . '.',
            'action_url' => route('payments.show', $payment->id),
            'action_label' => 'View Payment',
            'icon' => 'credit-card',
            'metadata' => ['payment_id' => $payment->id, 'customer_id' => $payment->customer_id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function invoiceCreated($invoice): void
    {
        $recipients = $this->recipientsForPermission(
            'invoice.view',
            $invoice->company_id ?? null
        );

        $this->create([
            'type' => 'invoice_created',
            'title' => 'New Invoice',
            'message' => 'Invoice #' . $invoice->invoice_number . ' created for ' . $invoice->customer->name . '.',
            'action_url' => route('invoices.show', $invoice->id),
            'action_label' => 'View Invoice',
            'icon' => 'file-text',
            'metadata' => ['invoice_id' => $invoice->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function invoiceOverdue($invoice): void
    {
        $recipients = $this->recipientsForPermission(
            'invoice.view',
            $invoice->company_id ?? null
        );

        $this->create([
            'type' => 'invoice_overdue',
            'title' => 'Invoice Overdue',
            'message' => 'Invoice #' . $invoice->invoice_number . ' is overdue. Amount: ₦' . number_format($invoice->due_amount, 2),
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
        $driverUser = $delivery->driver?->user;

        $extras = $driverUser instanceof User ? [$driverUser] : [];

        $recipients = $this->recipientsForPermission(
            'delivery.view',
            $delivery->company_id ?? $order?->company_id ?? null,
            $this->branchOf($order),
            $extras
        );

        $statusMessages = [
            'in_transit' => 'is now in transit',
            'delivered' => 'has been delivered',
            'failed' => 'delivery attempt failed',
            'assigned' => 'has been assigned to a driver',
            'out_for_delivery' => 'is out for delivery',
            'pending' => 'is pending',
            'returned' => 'was returned',
            'cancelled' => 'was cancelled',
        ];

        $this->create([
            'type' => 'delivery_update',
            'title' => 'Delivery Update',
            'message' => 'Delivery for order #' . $order?->order_number . ' ' . ($statusMessages[$status] ?? "status: {$status}") . '.',
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
        $recipients = $this->recipientsForPermission(
            'stock.view',
            $product->company_id ?? null
        );

        $this->create([
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "{$product->name} is running low. Current stock: {$currentQty}.",
            'action_url' => route('products.show', $product->id),
            'action_label' => 'View Product',
            'icon' => 'alert-triangle',
            'metadata' => ['product_id' => $product->id, 'current_qty' => $currentQty],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }

    public function grnCompleted($grn): void
    {
        $recipients = $this->recipientsForPermission(
            'grn.view',
            $grn->company_id ?? null
        );

        $this->create([
            'type' => 'grn_completed',
            'title' => 'Goods Received',
            'message' => "GRN #{$grn->grn_number} has been completed.",
            'action_url' => route('grn.show', $grn->id),
            'action_label' => 'View GRN',
            'icon' => 'package',
            'metadata' => ['grn_id' => $grn->id],
            'recipients' => $recipients,
            'email' => true,
        ]);
    }
}