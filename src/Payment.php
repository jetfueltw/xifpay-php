<?php

namespace Jetfuel\Xifpay;

use Jetfuel\Xifpay\HttpClient\GuzzleHttpClient;
use Jetfuel\Xifpay\Traits\ConvertMoney;

class Payment
{
    use ConvertMoney;

    const BASE_API_URL   = 'https://ebank.xifpay.com/payment/v1/order/';
    const GOODS_BODY     = 'GOODS_BODY';
    const GOODS_NAME     = 'GOODS_NAME';
    const PAY_METHOD     = 'directPay';
    const SERVICE        = 'online_pay';
    const SIGN_TYPE      = 'SHA';
    const TIME_ZONE      = 'Asia/Shanghai';
    const TIME_FORMAT    = 'YmdHis';
    const PAYMENT_EXPIRE = 8 * 60;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $baseApiUrl;

    /**
     * @var \Jetfuel\Xifpay\HttpClient\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Payment constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    protected function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        $this->merchantId = $merchantId;
        $this->secretKey = $secretKey;
        $this->baseApiUrl = $baseApiUrl === null ? self::BASE_API_URL : $baseApiUrl;

        $this->httpClient = new GuzzleHttpClient($this->baseApiUrl);
    }

    /**
     * Sign request payload.
     *
     * @param array $payload
     * @return array
     */
    protected function signPayload(array $payload)
    {
        $payload['merchantId'] = $this->merchantId;
        $payload['sign'] = Signature::generate($payload, $this->secretKey);
        $payload['signType'] = self::SIGN_TYPE;
        //$payload['apikey'] = $this->secretKey;

        return $payload;
    }

    /**
     * Get current time.
     *
     * @return string
     */
    protected function getCurrentTime()
    {
        return (new \DateTime('now', new \DateTimeZone(self::TIME_ZONE)))->format(self::TIME_FORMAT);
    }

    /**
     * Get payment expire time.
     *
     * @return string
     */
    protected function getExpireTime()
    {
        return (new \DateTime('now +'.self::PAYMENT_EXPIRE.' minute', new \DateTimeZone(self::TIME_ZONE)))->format(self::TIME_FORMAT);
    }
}
