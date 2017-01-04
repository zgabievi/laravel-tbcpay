<?php

namespace Gabievi\TBC;

class TBC
{
    use TBCTrait;
    
    /**
     * @var string
     */
    private $submit_url = 'https://securepay.ufc.ge:18443/ecomm2/MerchantHandler';

    /**
     * @var
     */
    private $cert_path;

    /**
     * @var
     */
    private $cert_pass;

    /**
     * @var
     */
    private $client_ip;

    /**
     * TBC constructor.
     */
    public function __construct()
    {
        $this->cert_path = config('tbc.cert_path');
        $this->cert_pass = config('tbc.cert_pass');

        $this->client_ip = request()->getClientIp();
    }

    /**
     * @param string $command
     * @param array  $data
     *
     * @return array
     */
    private function process($command, array $data = [])
    {
        return $this->parse(
            $this->cURL(
                $this->queryString(
                    array_merge([
                        'command' => $command,
                        $data,
                    ])
                )
            )
        );
    }

    /**
     * @param $amount
     * @param int    $currency
     * @param string $description
     * @param string $language
     *
     * @return array
     */
    public function SMSTransaction($amount, $currency = 981, $description = '', $language = 'GE')
    {
        return $this->transaction('v', $amount, $currency, $description, $language);
    }

    /**
     * @param $amount
     * @param int    $currency
     * @param string $description
     * @param string $language
     *
     * @return array
     */
    public function DMSAuthorization($amount, $currency = 981, $description = '', $language = 'GE')
    {
        return $this->transaction('a', $amount, $currency, $description, $language, 'DMS');
    }

    /**
     * @param $txn_id
     * @param $amount
     * @param int    $currency
     * @param string $description
     * @param string $language
     *
     * @return array
     */
    public function DMSTransaction($txn_id, $amount, $currency = 981, $description = '', $language = 'GE')
    {
        return $this->transaction('t', $amount, $currency, $description, $language, 'DMS', [
            'trans_id' => $txn_id,
        ]);
    }

    /**
     * @param string $command
     * @param $amount
     * @param int    $currency
     * @param string $description
     * @param string $language
     * @param string $type
     * @param array  $additional
     *
     * @return array
     */
    private function transaction($command, $amount, $currency = 981, $description = '', $language = 'GE', $type = 'SMS', array $additional = [])
    {
        return $this->process(
            $command,
            array_merge([
                'amount'         => $amount,
                'currency'       => $currency,
                'client_ip_addr' => $this->client_ip,
                'description'    => $description,
                'language'       => $language,
                'msg_type'       => $type,
            ], $additional)
        );
    }

    /**
     * @param $txn_id
     *
     * @return array
     */
    public function getTransactionResult($txn_id)
    {
        return $this->process('c', [
            'trans_id'       => $txn_id,
            'client_ip_addr' => $this->client_ip,
        ]);
    }

    /**
     * @param $txn_id
     * @param $amount
     * @param string $suspected_fraud
     *
     * @return array
     */
    public function reverseTransaction($txn_id, $amount = '', $suspected_fraud = '')
    {
        return $this->process('r', [
            'trans_id'        => $txn_id,
            'amount'          => $amount,
            'suspected_fraud' => $suspected_fraud,
        ]);
    }

    /**
     * @param $txn_id
     *
     * @return array
     */
    public function refundTransaction($txn_id)
    {
        return $this->process('k', [
            'trans_id' => $txn_id,
        ]);
    }

    /**
     * @param $txn_id
     * @param $amount
     *
     * @return array
     */
    public function creditTransaction($txn_id, $amount = '')
    {
        return $this->process('g', [
            'trans_id' => $txn_id,
            'amount'   => $amount,
        ]);
    }

    /**
     * @return array
     */
    public function closeDay()
    {
        return $this->process('b');
    }
}
