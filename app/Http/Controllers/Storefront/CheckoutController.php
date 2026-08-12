<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Customers\Customer;
use App\Models\Invoicing\Invoice;
use App\Models\Invoicing\InvoiceItem;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentAllocation;
use App\Models\Products\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $cart = session()->get('cart', []);
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $product = Product::with(['category', 'brand', 'unit'])
                ->where('id', $item['product_id'])
                ->first();

            if ($product) {
                $lineTotal = $product->selling_price * $item['quantity'];
                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->selling_price,
                    'quantity' => $item['quantity'],
                    'line_total' => $lineTotal,
                ];
                $subtotal += $lineTotal;
            }
        }

        if (empty($items)) {
            return redirect()->route('storefront.cart');
        }

        $taxRate = 7.5;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return Inertia::render('Storefront/Checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate,
            'taxAmount' => $taxAmount,
            'total' => $total,
            'customer' => $customer,
            'cartCount' => count($items),
        ]);
    }

    public function placeOrder(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'shipping_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('storefront.cart')->withErrors(['cart' => 'Your cart is empty']);
        }

        $company = \App\Models\Companies\Company::firstOrFail();

        $result = DB::transaction(function () use ($cart, $customer, $company, $request) {
            $subtotal = 0;
            $taxAmount = 0;
            $lineItems = [];

            foreach ($cart as $item) {
                $product = Product::where('id', $item['product_id'])->first();
                if (!$product) continue;

                $lineTotal = $product->selling_price * $item['quantity'];
                $tax = $lineTotal * 0.075;
                $subtotal += $lineTotal;
                $taxAmount += $tax;

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'tax_amount' => $tax,
                    'total' => $lineTotal + $tax,
                ];
            }

            $total = $subtotal + $taxAmount;

            $order = Order::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'fulfillment_status' => 'unfulfilled',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'order_date' => now(),
            ]);

            foreach ($lineItems as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'sku' => $line['product']->sku,
                    'name' => $line['product']->name,
                    'unit_id' => $line['product']->unit_id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_rate' => 7.5,
                    'tax_amount' => $line['tax_amount'],
                    'line_total' => $line['total'],
                ]);
            }

            $invoice = Invoice::create([
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'invoice_type' => 'sales',
                'status' => 'draft',
                'invoice_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'currency_code' => 'NGN',
                'due_date' => now()->addDays(30),
            ]);

            foreach ($lineItems as $line) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $line['product']->id,
                    'sku' => $line['product']->sku,
                    'name' => $line['product']->name,
                    'unit_id' => $line['product']->unit_id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'tax_rate' => 7.5,
                    'tax_amount' => $line['tax_amount'],
                    'line_total' => $line['total'],
                ]);
            }

            return ['order' => $order, 'invoice' => $invoice];
        });

        session()->forget('cart');

        return redirect()->route('storefront.payment', [
            'invoice' => $result['invoice']->id,
        ]);
    }

    public function payment(string $invoiceId)
    {
        $customer = Auth::guard('customer')->user();

        $invoice = Invoice::where('id', $invoiceId)
            ->where('customer_id', $customer->id)
            ->with(['items.product', 'order', 'payments'])
            ->firstOrFail();

        $isPaid = $invoice->status === 'paid';

        return Inertia::render('Storefront/Payment', [
            'invoice' => $invoice,
            'isPaid' => $isPaid,
            'cartCount' => 0,
        ]);
    }

    public function submitPayment(Request $request, string $invoiceId)
    {
        $customer = Auth::guard('customer')->user();

        $invoice = Invoice::where('id', $invoiceId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        if ($invoice->status === 'paid') {
            return back()->withErrors(['invoice' => 'This invoice is already fully paid.']);
        }

        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->due_amount,
            'reference_number' => 'nullable|string|max:100',
        ]);

        $receiptPath = $request->file('receipt')->store('payment-receipts', 'public');

        $payment = Payment::create([
            'company_id' => $invoice->company_id,
            'customer_id' => $customer->id,
            'payment_type' => 'incoming',
            'payment_method' => 'bank_transfer',
            'reference_number' => $request->reference_number,
            'status' => 'pending',
            'amount' => $request->amount,
            'currency_code' => 'NGN',
            'payment_date' => now(),
            'notes' => "Receipt uploaded by customer for Invoice {$invoice->invoice_number}. Ref: {$request->reference_number}",
            'metadata' => json_encode([
                'receipt_path' => $receiptPath,
                'reference_number' => $request->reference_number,
                'uploaded_by' => 'customer',
            ]),
        ]);

        PaymentAllocation::create([
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
        ]);

        return redirect()->route('storefront.orderConfirmation', ['order' => $invoice->order_id]);
    }

    public function orderConfirmation(string $orderId)
    {
        $customer = Auth::guard('customer')->user();

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customer->id)
            ->with(['invoice.payments'])
            ->firstOrFail();

        $invoice = $order->invoice;

        return Inertia::render('Storefront/OrderConfirmation', [
            'order' => $order,
            'invoice' => $invoice,
            'cartCount' => 0,
        ]);
    }

    public function account()
    {
        $customer = Auth::guard('customer')->user();

        $orders = Order::where('customer_id', $customer->id)
            ->with(['invoice'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Storefront/Account', [
            'customer' => $customer,
            'orders' => $orders,
            'cartCount' => 0,
        ]);
    }
}
