<?php

namespace Gabievi\TBC;

trait TBCTrait {
     
     /**
     * @param string $query
     *
     * @return mixed
     */
    private function cURL($query)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        curl_setopt($curl, CURLOPT_VERBOSE, '1');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSLCERT, $this->certPath);
        curl_setopt($curl, CURLOPT_SSLKEY, $this->certPath);
        curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $this->certPass);
        curl_setopt($curl, CURLOPT_URL, $this->submitUri);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function queryString(array $data = [])
    {
        return http_build_query($data);
    }

    /**
     * @param $string
     *
     * @return array
     */
    private function parse($string)
    {
        $params = explode(PHP_EOL, trim($string));
        $result = [];

        foreach ($params as $param) {
            $parts = explode(':', $param);
            $result[$parts[0]] = trim($parts[1]);
        }

        return $result;
    }
}
