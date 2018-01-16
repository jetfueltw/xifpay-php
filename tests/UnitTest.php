<?php

namespace Test;

use Faker\Factory;
use Jetfuel\Xifpay\BankPayment;
use Jetfuel\Xifpay\Constants\Bank;
use Jetfuel\Xifpay\Constants\Channel;
use Jetfuel\Xifpay\DigitalPayment;
use Jetfuel\Xifpay\TradeQuery;
use Jetfuel\Xifpay\Traits\NotifyWebhook;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    private $merchantId;
    private $secretKey;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->merchantId = getenv('MERCHANT_ID');
        $this->secretKey = getenv('SECRET_KEY');
    }

    public function testDigitalPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = $tradeNo = date('YmdHis').rand(10000, 99999);
        $channel = Channel::WECHAT;
        $amount = 1;
        $clientIp = $faker->ipv4;
        $notifyUrl = $faker->url;
        $returnUrl = $faker->url;

        $payment = new DigitalPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $channel, $amount, $clientIp, $notifyUrl, $returnUrl);
        var_dump($result);
        $this->assertEquals('S0001', $result['respCode']);

        return $tradeNo;
    }

    /**
     * * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
   */
    public function testDigitalPaymentOrderFind($tradeNo)
    {
        
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        var_dump($result);
        $this->assertEquals('S0001', $result['respCode']);
    }

    /**
     * @depends testDigitalPaymentOrder
     *
     * @param $tradeNo
     */
    public function testDigitalPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testBankPaymentOrder()
    {
        $faker = Factory::create();
        $tradeNo = $tradeNo = date('YmdHis').rand(10000, 99999);
        $bank = Bank::CCB;
        $amount = 1;
        $returnUrl = $faker->url;
        $notifyUrl = $faker->url;

        $payment = new BankPayment($this->merchantId, $this->secretKey);
        $result = $payment->order($tradeNo, $bank, $amount, $notifyUrl, $returnUrl);

        $this->assertContains('<form', $result, '', true);

        return $tradeNo;
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderFind($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);

        var_dump($result);
        $this->assertEquals('S0001', $result['respCode']);
    }

    /**
     * @depends testBankPaymentOrder
     *
     * @param $tradeNo
     */
    public function testBankPaymentOrderIsPaid($tradeNo)
    {
        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testTradeQueryFindOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = $faker->uuid;

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->find($tradeNo);
        var_dump($result);

        $this->assertEquals('F2001', $result['respCode']);
    }

    public function testTradeQueryIsPaidOrderNotExist()
    {
        $faker = Factory::create();
        $tradeNo = $faker->uuid;

        $tradeQuery = new TradeQuery($this->merchantId, $this->secretKey);
        $result = $tradeQuery->isPaid($tradeNo);

        $this->assertFalse($result);
    }

    public function testNotifyWebhookVerifyNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'body'          => 'GOODS_BODY',
            'tradeNo'       => '101708071024872',
            'ext_param2'    => 'ALIPAY',
            'gmt_create'    => '2017-08-07 06:08:08',
            'gmt_payment'   => '2017-08-07 08:08:08',
            'is_sucess'     => 'T',
            'notify_id'     => '1111',
            'notify_time'   => '2017-08-07 09:08:08',
            'notify_type'   => 'WAIT_TRIGGER',
            'order_no'      => 'AAAA00001',
            'payment_type'  => '1',
            'seller_actions'=> 'SEND_GOODS',
            'title'         => 'GOODS_NAME',
            'total_fee'     => '1.00',
            'trade_no'      => 'aaabbb0001',
            'trade_status'  => 'TRADE_FINISHED',
            'use_coupon'    => 'N',
            'sign_type'     => 'SHA',
            'sign'          => '75E0832B2D2B744F02791558DB8E8DD447D85ECD',
        ];

        $this->assertTrue($mock->verifyNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookParseNotifyPayload()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $payload = [
            'body'          => 'GOODS_BODY',
            'tradeNo'       => '101708071024872',
            'ext_param2'    => 'ALIPAY',
            'gmt_create'    => '2017-08-07 06:08:08',
            'gmt_payment'   => '2017-08-07 08:08:08',
            'is_sucess'     => 'T',
            'notify_id'     => '1111',
            'notify_time'   => '2017-08-07 09:08:08',
            'notify_type'   => 'WAIT_TRIGGER',
            'order_no'      => 'AAAA00001',
            'payment_type'  => '1',
            'seller_actions'=> 'SEND_GOODS',
            'title'         => 'GOODS_NAME',
            'total_fee'     => '1.00',
            'trade_no'      => 'aaabbb0001',
            'trade_status'  => 'TRADE_FINISHED',
            'use_coupon'    => 'N',
            'sign_type'     => 'SHA',
            'sign'          => '75E0832B2D2B744F02791558DB8E8DD447D85ECD',
        ];

        $this->assertEquals([
            'body'          => 'GOODS_BODY',
            'tradeNo'       => '101708071024872',
            'ext_param2'    => 'ALIPAY',
            'gmt_create'    => '2017-08-07 06:08:08',
            'gmt_payment'   => '2017-08-07 08:08:08',
            'is_sucess'     => 'T',
            'notify_id'     => '1111',
            'notify_time'   => '2017-08-07 09:08:08',
            'notify_type'   => 'WAIT_TRIGGER',
            'order_no'      => 'AAAA00001',
            'payment_type'  => '1',
            'seller_actions'=> 'SEND_GOODS',
            'title'         => 'GOODS_NAME',
            'total_fee'     => '1.00',
            'trade_no'      => 'aaabbb0001',
            'trade_status'  => 'TRADE_FINISHED',
            'use_coupon'    => 'N',
            'sign_type'     => 'SHA',
            'sign'          => '75E0832B2D2B744F02791558DB8E8DD447D85ECD',
        ], $mock->parseNotifyPayload($payload, $this->secretKey));
    }

    public function testNotifyWebhookSuccessNotifyResponse()
    {
        $mock = $this->getMockForTrait(NotifyWebhook::class);

        $this->assertEquals('success', $mock->successNotifyResponse());
    }
}
