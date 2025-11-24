<?php

namespace Tests\Unit;

use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Traits\Payment;
use Mockery;
use Tests\TestCase;

class PaymentLinkGenerationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_link_builds_saman_url(): void
    {
        $mock = Mockery::mock('overload:App\Models\PaymentRequest');
        $mock->id = 'test-saman-id';
        $mock->shouldReceive('save')->andReturnTrue();

        $payer = new Payer('Test User', 'test@example.com', '123456789', '');
        $paymentInfo = new PaymentInfo(
            success_hook: 'strtolower',
            failure_hook: 'strtoupper',
            currency_code: 'IRR',
            payment_method: 'saman',
            payment_platform: 'web',
            payer_id: 'user-1',
            receiver_id: 'receiver-1',
            additional_data: ['note' => 'testing'],
            payment_amount: 1000,
            external_redirect_link: null,
            attribute: 'order',
            attribute_id: 'order-1'
        );
        $receiver = new Receiver('Receiver', 'example.png');

        $link = Payment::generate_link($payer, $paymentInfo, $receiver);

        $this->assertIsString($link);
        $this->assertStringContainsString('/payment/saman/pay/?payment_id=test-saman-id', $link);
    }

    public function test_generate_link_builds_pasargad_url(): void
    {
        $mock = Mockery::mock('overload:App\Models\PaymentRequest');
        $mock->id = 'test-pasargad-id';
        $mock->shouldReceive('save')->andReturnTrue();

        $payer = new Payer('Test User', 'test@example.com', '123456789', '');
        $paymentInfo = new PaymentInfo(
            success_hook: 'strtolower',
            failure_hook: 'strtoupper',
            currency_code: 'IRT',
            payment_method: 'pasargad',
            payment_platform: 'web',
            payer_id: 'user-1',
            receiver_id: 'receiver-1',
            additional_data: ['note' => 'testing'],
            payment_amount: 2000,
            external_redirect_link: null,
            attribute: 'order',
            attribute_id: 'order-2'
        );
        $receiver = new Receiver('Receiver', 'example.png');

        $link = Payment::generate_link($payer, $paymentInfo, $receiver);

        $this->assertIsString($link);
        $this->assertStringContainsString('/payment/pasargad/pay/?payment_id=test-pasargad-id', $link);
    }
}
