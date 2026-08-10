<?php

namespace Database\Seeders\Settings;

use App\Models\Companies\Company;
use App\Models\Settings\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'company_name', 'value' => null, 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'company_email', 'value' => null, 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'company_phone', 'value' => null, 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'company_address', 'value' => null, 'type' => 'text', 'is_public' => true],
            ['group' => 'general', 'key' => 'currency_code', 'value' => 'NGN', 'type' => 'string', 'is_public' => true],
            ['group' => 'general', 'key' => 'timezone', 'value' => 'Africa/Lagos', 'type' => 'string', 'is_public' => false],
            ['group' => 'general', 'key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'is_public' => false],
            ['group' => 'order', 'key' => 'order_number_prefix', 'value' => 'ORD-', 'type' => 'string', 'is_public' => false],
            ['group' => 'order', 'key' => 'order_number_start', 'value' => '1000', 'type' => 'integer', 'is_public' => false],
            ['group' => 'order', 'key' => 'auto_confirm_orders', 'value' => 'false', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'invoice', 'key' => 'invoice_number_prefix', 'value' => 'INV-', 'type' => 'string', 'is_public' => false],
            ['group' => 'invoice', 'key' => 'invoice_number_start', 'value' => '1000', 'type' => 'integer', 'is_public' => false],
            ['group' => 'invoice', 'key' => 'default_payment_terms', 'value' => '30', 'type' => 'integer', 'is_public' => false],
            ['group' => 'invoice', 'key' => 'invoice_due_days', 'value' => '30', 'type' => 'integer', 'is_public' => false],
            ['group' => 'payment', 'key' => 'payment_number_prefix', 'value' => 'PAY-', 'type' => 'string', 'is_public' => false],
            ['group' => 'payment', 'key' => 'payment_number_start', 'value' => '1000', 'type' => 'integer', 'is_public' => false],
            ['group' => 'payment', 'key' => 'require_payment_approval', 'value' => 'true', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'stock', 'key' => 'low_stock_threshold', 'value' => '10', 'type' => 'integer', 'is_public' => false],
            ['group' => 'stock', 'key' => 'enable_stock_reservations', 'value' => 'true', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'delivery', 'key' => 'max_delivery_attempts', 'value' => '3', 'type' => 'integer', 'is_public' => false],
            ['group' => 'delivery', 'key' => 'delivery_number_prefix', 'value' => 'DEL-', 'type' => 'string', 'is_public' => false],
            ['group' => 'notification', 'key' => 'email_notifications', 'value' => 'true', 'type' => 'boolean', 'is_public' => false],
            ['group' => 'notification', 'key' => 'sms_notifications', 'value' => 'false', 'type' => 'boolean', 'is_public' => false],
        ];

        $companies = Company::all();

        foreach ($companies as $company) {
            foreach ($settings as $setting) {
                $value = $setting['value'];
                if ($setting['key'] === 'company_name') {
                    $value = $company->name;
                } elseif ($setting['key'] === 'company_email') {
                    $value = $company->email;
                } elseif ($setting['key'] === 'company_phone') {
                    $value = $company->phone;
                } elseif ($setting['key'] === 'company_address') {
                    $value = $company->address_line_1;
                } elseif ($setting['key'] === 'currency_code') {
                    $value = $company->currency_code;
                }

                Setting::firstOrCreate(
                    ['company_id' => $company->id, 'key' => $setting['key']],
                    [
                        'id' => \Illuminate\Support\Str::uuid(),
                        'group' => $setting['group'],
                        'value' => $value,
                        'type' => $setting['type'],
                        'is_public' => $setting['is_public'],
                    ]
                );
            }
        }
    }
}
