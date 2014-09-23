<?php

class API {

    public $use_24h_avg = false;

    public function getAddressBalance($address, $confirmations=0)
    {
        try {
            $balance = $this->curl('http://blockchain.info/nl/q/addressbalance/'.$address.'?confirmations='.$confirmations);
            return $balance/100000000;
            //return $this->curl('http://blockexplorer.com/q/getreceivedbyaddress/'.$address.'/'.$confirmations);
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
        }
    }

    public function getAddressHistory($address)
    {
        try {
            return $this->curl('http://blockchain.info/rawaddr/'.$address);
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
        }
    }

    public function getCurrentPrice()
    {
        try {
            $ticker = $this->curl('https://api.bitcoinaverage.com/ticker/global/USD/');

            if($this->use_24h_avg)    {
                return $ticker->{'24h_avg'};
            }   else    {
                return $ticker->last;
            }
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
        }
    }

    public function getReceiveAddress($address=null, $secret=null)
    {
        if(!$address)   $address = SBTCP_RECEIVE_ADDR;
        $callback_url = SBTCP_CALLBACK_URL;

        try {
            $response =  $this->curl('https://blockchain.info/api/receive?method=create&address='.$address.'&callback='.$callback_url);
            //echo '<pre>'.print_r($response, true)."</pre>\n";

            //- could check output == SBTP_RECEIVE_ADDR for security
            if($response && property_exists($response, 'input_address'))   {
                return $response->input_address;
            }   else    {
                return false;
            }
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
        }
    }

    public function getTransaction($hash)
    {
        try {
            return $this->curl('http://blockchain.info/rawtx/'.$hash);
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
        }
    }

    public function curl($url, $payload=null)
    {

        $options = array(
                            CURLOPT_URL             => $url,
                            CURLOPT_HEADER          => false,
                            CURLOPT_FORBID_REUSE    => true,
                            CURLOPT_FRESH_CONNECT   => true,
                            CURLOPT_TIMEOUT         => 4,
                            CURLOPT_RETURNTRANSFER  => true,
                            CURLOPT_USERAGENT       => 'simplebtcpay v1',
                            CURLOPT_COOKIESESSION   => true,
                            CURLOPT_SSL_VERIFYPEER  => false,
                        );

        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $headers[] = 'Accept-Language: en-US';
        $headers[] = 'Expect:';

        if($payload) {
            $payload_json = json_encode($payload);
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POST]  = true;
            $options[CURLOPT_POSTFIELDS]    = $payload_json;
            $headers[]  = 'Content-Length: ' . strlen($payload_json);
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

        if(curl_errno($ch)){
            throw new Exception(curl_error($ch));
        }

        $response = json_decode($response);
        //error_log('curl.response: '. print_r($response,true));

        return $response;
    }
}
