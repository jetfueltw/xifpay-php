<?php

namespace Jetfuel\Xifpay;

use Jetfuel\Xifpay\Traits\ResultParser;

class DigitalPayment extends Payment
{
    use ResultParser;
    const APP = 'app';

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
     * @param int $channel
     * @param float $amount
     * @param string $clientIp
     * @param string $notifyUrl
     * @return array
     */
    public function order($tradeNo, $channel, $amount, $clientIp, $notifyUrl, $returnUrl)
    {
        $payload = $this->signPayload([
            'body'            => self::GOODS_BODY,
            'charset'         => 'UTF-8',
            'defaultbank'     => $channel,
            'isApp'           => self::APP,
            'notifyUrl'       => $notifyUrl,
            'orderNo'         => $tradeNo,
            'paymentType'     => '1',
            'paymethod'       => self::PAY_METHOD,
            'returnUrl'       => $returnUrl,
            'service'         => self::SERVICE,
            'title'           => self::GOODS_NAME,
            'totalFee'        => $amount,
        ]);

        return $this->parseResponse($this->httpClient->post($this->merchantId .'-'  . $tradeNo, $payload));
    }
}
