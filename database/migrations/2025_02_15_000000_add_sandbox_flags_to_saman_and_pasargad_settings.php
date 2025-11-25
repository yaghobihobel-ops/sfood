<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('addon_settings')) {
            return;
        }

        $gateways = [
            'saman' => [
                'gateway' => 'saman',
                'mode' => 'test',
                'status' => '0',
                'merchant_id' => '',
                'terminal_id' => '',
                'payment_url' => 'https://sep.shaparak.ir/payment.aspx',
                'callback_url' => url('payment/saman/callback'),
                'sandbox' => '1',
            ],
            'pasargad' => [
                'gateway' => 'pasargad',
                'mode' => 'test',
                'status' => '0',
                'merchant_code' => '',
                'terminal_code' => '',
                'payment_url' => 'https://pep.shaparak.ir/payment.aspx',
                'cert_path' => '',
                'currency_multiplier' => 'toman_to_rial',
                'callback_url' => url('payment/pasargad/callback'),
                'sandbox' => '1',
            ],
        ];

        foreach ($gateways as $key => $defaults) {
            $existing = Setting::where('key_name', $key)
                ->where('settings_type', 'payment_config')
                ->first();

            $liveValues = array_merge($defaults, $existing?->live_values ?? []);
            $testValues = array_merge($defaults, $existing?->test_values ?? []);

            Setting::updateOrCreate(
                [
                    'key_name' => $key,
                    'settings_type' => 'payment_config',
                ],
                [
                    'live_values' => $liveValues,
                    'test_values' => $testValues,
                    'mode' => $existing->mode ?? 'test',
                    'is_active' => $existing->is_active ?? 0,
                    'additional_data' => $existing->additional_data ?? json_encode([
                        'gateway_title' => ucfirst($key),
                        'gateway_image' => '',
                        'storage' => 'public',
                    ]),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('addon_settings')) {
            return;
        }

        foreach (['saman', 'pasargad'] as $key) {
            $setting = Setting::where('key_name', $key)
                ->where('settings_type', 'payment_config')
                ->first();

            if ($setting) {
                $live = $setting->live_values;
                $test = $setting->test_values;

                unset($live['sandbox'], $test['sandbox']);

                $setting->update([
                    'live_values' => $live,
                    'test_values' => $test,
                ]);
            }
        }
    }
};
