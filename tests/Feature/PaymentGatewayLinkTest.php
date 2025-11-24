<?php

namespace Tests\Feature;

use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PaymentGatewayLinkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);

        Schema::create('payment_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('success_hook')->nullable();
            $table->string('failure_hook')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('payer_id')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->longText('additional_data')->nullable();
            $table->double('payment_amount')->default(0);
            $table->string('external_redirect_link')->nullable();
            $table->string('attribute')->nullable();
            $table->string('attribute_id')->nullable();
            $table->string('payment_platform')->nullable();
            $table->tinyInteger('is_paid')->default(0);
            $table->string('transaction_id')->nullable();
            $table->longText('payer_information')->nullable();
            $table->longText('receiver_information')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('payment_requests');
        parent::tearDown();
    }

    public function test_generate_link_supports_saman_gateway(): void
    {
        $payer = new Payer('Customer', 'customer@example.com', '123456', '');
        $receiver = new Receiver('receiver', 'logo.png');

        $paymentInfo = new PaymentInfo(
            success_hook: 'order_place',
            failure_hook: 'order_failed',
            currency_code: 'IRR',
            payment_method: 'saman',
            payment_platform: 'web',
            payer_id: 1,
            receiver_id: 2,
            additional_data: [],
            payment_amount: 1000,
            external_redirect_link: null,
            attribute: 'order',
            attribute_id: 10
        );

        $link = \App\Traits\Payment::generate_link($payer, $paymentInfo, $receiver);

        $this->assertIsString($link);
        $this->assertStringContainsString('payment/saman/pay', $link);
    }

    public function test_generate_link_supports_pasargad_gateway(): void
    {
        $payer = new Payer('Customer', 'customer@example.com', '123456', '');
        $receiver = new Receiver('receiver', 'logo.png');

        $paymentInfo = new PaymentInfo(
            success_hook: 'order_place',
            failure_hook: 'order_failed',
            currency_code: 'IRR',
            payment_method: 'pasargad',
            payment_platform: 'web',
            payer_id: 1,
            receiver_id: 2,
            additional_data: [],
            payment_amount: 1200,
            external_redirect_link: null,
            attribute: 'order',
            attribute_id: 11
        );

        $link = \App\Traits\Payment::generate_link($payer, $paymentInfo, $receiver);

        $this->assertIsString($link);
        $this->assertStringContainsString('payment/pasargad/pay', $link);
    }
}
