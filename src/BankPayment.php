<?php

namespace Jetfuel\Xifpay;

class BankPayment extends Payment
{
    const WEB = 'web';
    /**
     * BankPayment constructor.
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
     * Create bank payment order.
     *
     * @param string $tradeNo
     * @param string $bank
     * @param float $amount
     * @param string $notifyUrl
     * @param string $returnUrl
     * @return string
     */
    public function order($tradeNo, $bank, $amount, $notifyUrl, $returnUrl)
    {
        $payload = $this->signPayload([
            'body'            => self::GOODS_BODY,
            'charset'         => 'UTF-8',
            'defaultbank'     => $bank,
            'isApp'           => self::WEB,
            'notifyUrl'       => $notifyUrl,
            'orderNo'         => $tradeNo,
            'paymentType'     => '1',
            'paymethod'       => self::PAY_METHOD,
            'returnUrl'       => $returnUrl,
            'service'         => self::SERVICE,
            'title'           => self::GOODS_NAME,
            'totalFee'        => $amount,
        ]);

        return $this->httpClient->post($this->merchantId .'-'  . $tradeNo, $payload);
    }
}
