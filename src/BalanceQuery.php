<?php

namespace Jetfuel\Xifpay;

use Jetfuel\Xifpay\Traits\ResultParser;

class BalanceQuery extends Payment
{
    use ResultParser;

    /**
     * BalanceQuery constructor.
     *
     * @param string $merchantId
     * @param string $secretKey
     * @param null|string $baseApiUrl
     */
    public function __construct($merchantId, $secretKey, $baseApiUrl = null)
    {
        parent::__construct($merchantId, $secretKey, 'https://client.xifpay.com/search/');
    }

    /**
     * Query Balance.
     *
     * @param 
     * @return array|null
     */
    public function query()
    {
        $payload = $this->signBalancePayload([]);

        $balance = $this->parseXMLResponse($this->httpClient->get('queryBalance', $payload));
       
        return $balance;
    }

}
