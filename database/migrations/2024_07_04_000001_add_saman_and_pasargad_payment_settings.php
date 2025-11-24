<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('addon_settings')) {
            return;
        }

        $defaultGateways = [
            'saman' => [
                'gateway' => 'saman',
                'mode' => 'test',
                'status' => '0',
                'merchant_id' => '',
                'terminal_id' => '',
                'payment_url' => 'https://sep.shaparak.ir/payment.aspx',
                'callback_url' => url('payment/saman/callback'),
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
            ],
        ];

        foreach ($defaultGateways as $key => $values) {
            Setting::updateOrCreate(
                [
                    'key_name' => $key,
                    'settings_type' => 'payment_config',
                ],
                [
                    'live_values' => $values,
                    'test_values' => $values,
                    'mode' => 'test',
                    'is_active' => 0,
                    'additional_data' => json_encode([
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
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('addon_settings')) {
            return;
        }

        Setting::whereIn('key_name', ['saman', 'pasargad'])
            ->where('settings_type', 'payment_config')
            ->delete();
    }
};
