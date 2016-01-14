<?php

namespace Gabievi\TBC;

class TBC
{

	/**
	 * @type string
	 */
	private $submit_url = 'https://securepay.ufc.ge:18443/ecomm2/MerchantHandler';

	/**
	 * @type
	 */
	private $cert_path;

	/**
	 * @type
	 */
	private $cert_pass;

	/**
	 * @type
	 */
	private $client_ip;

	/**
	 * TBC constructor.
	 */
	function __construct()
	{
		$this->cert_path = config('tbc.cert_path');
		$this->cert_pass = config('tbc.cert_pass');

		$this->client_ip = request()->getClientIp();
	}

	/**
	 * @param $query_string
	 *
	 * @return mixed
	 */
	private function cURL($query_string)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POSTFIELDS, $query_string);
		curl_setopt($curl, CURLOPT_VERBOSE, '1');
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSLCERT, $this->cert_path);
		curl_setopt($curl, CURLOPT_SSLKEY, $this->cert_path);
		curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $this->cert_pass);
		curl_setopt($curl, CURLOPT_URL, $this->submit_url);
		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}

	/**
	 * @param $post_fields
	 *
	 * @return string
	 */
	private function QueryString($post_fields)
	{
		return http_build_query($post_fields);
	}

	/**
	 * @param $string
	 *
	 * @return array
	 */
	private function Parse($string)
	{
		$array1 = explode(PHP_EOL, trim($string));
		$result = [];

		foreach ($array1 as $key => $value) {
			$array2 = explode(':', $value);
			$result[$array2[0]] = trim($array2[1]);
		}

		return $result;
	}

	/**
	 * @param $post_fields
	 *
	 * @return array
	 */
	private function Process($post_fields)
	{
		$string = $this->QueryString($post_fields);
		$result = $this->cURL($string);
		$parsed = $this->Parse($result);

		return $parsed;
	}

	/**
	 * @param $amount
	 * @param int $currency
	 * @param string $description
	 * @param string $language
	 *
	 * @return array
	 */
	public function SMSTransaction($amount, $currency = 981, $description = '', $language = 'GE')
	{
		return $this->Process([
			'command' => 'v',
			'amount' => $amount,
			'currency' => $currency,
			'client_ip_addr' => $this->client_ip,
			'description' => $description,
			'language' => $language,
			'msg_type' => 'SMS',
		]);
	}

	/**
	 * @param $amount
	 * @param int $currency
	 * @param string $description
	 * @param string $language
	 *
	 * @return array
	 */
	public function DMSAuthorization($amount, $currency = 981, $description = '', $language = 'GE')
	{
		return $this->Process([
			'command' => 'a',
			'amount' => $amount,
			'currency' => $currency,
			'client_ip_addr' => $this->client_ip,
			'description' => $description,
			'language' => $language,
			'msg_type' => 'DMS',
		]);
	}

	/**
	 * @param $txn_id
	 * @param $amount
	 * @param int $currency
	 * @param string $description
	 * @param string $language
	 *
	 * @return array
	 */
	public function DMSTransaction($txn_id, $amount, $currency = 981, $description = '', $language = 'GE')
	{
		return $this->Process([
			'command' => 't',
			'trans_id' => $txn_id,
			'amount' => $amount,
			'currency' => $currency,
			'client_ip_addr' => $this->client_ip,
			'description' => $description,
			'language' => $language,
			'msg_type' => 'DMS',
		]);
	}

	/**
	 * @param $txn_id
	 *
	 * @return array
	 */
	public function GetTransactionResult($txn_id)
	{
		return $this->Process([
			'command' => 'c',
			'trans_id' => $txn_id,
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
	public function ReverseTransaction($txn_id, $amount = '', $suspected_fraud = '')
	{
		return $this->Process([
			'command' => 'r',
			'trans_id' => $txn_id,
			'amount' => $amount,
			'suspected_fraud' => $suspected_fraud,
		]);
	}

	/**
	 * @param $txn_id
	 *
	 * @return array
	 */
	public function RefundTransaction($txn_id)
	{
		return $this->Process([
			'command' => 'k',
			'trans_id' => $txn_id,
		]);
	}

	/**
	 * @param $txn_id
	 * @param $amount
	 *
	 * @return array
	 */
	public function CreditTransaction($txn_id, $amount = '')
	{
		return $this->Process([
			'command' => 'g',
			'trans_id' => $txn_id,
			'amount' => $amount,
		]);
	}

	/**
	 * @return array
	 */
	public function CloseDay()
	{
		return $this->Process(['command' => 'b']);
	}
}
