<?php

namespace Jetfuel\Xifpay;

use Jetfuel\Xifpay\Traits\ResultParser;

class DigitalPayment extends Payment
{
    use ResultParser;

    const IS_APP = 'app';

    /**
     * DigitalPayment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        parent::__construct($merchantId, $secretKey, $baseApiUrl);
    }

    /**
     * Create digital payment order.
     *
     * @param string $tradeNo
     * @param string $channel
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $notifyUrl, $returnUrl)
    {
        $payload = $this->signPayload([
            'body'        => self::GOODS_BODY,
            'defaultbank' => $channel,
            'isApp'       => self::IS_APP,
            'notifyUrl'   => $notifyUrl,
            'orderNo'     => $tradeNo,
            'paymentType' => self::PAYMENT_TYPE,
            'paymethod'   => self::PAY_METHOD,
            'returnUrl'   => $returnUrl,
            'service'     => self::SERVICE,
            'title'       => self::GOODS_NAME,
            'totalFee'    => $amount,
        ]);

        return $this->parseResponse($this->httpClient->post($this->merchantId.'-'.$tradeNo, $payload));
    }
}
