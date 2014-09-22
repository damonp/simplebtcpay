<?php

class API {

    public $use_24h_avg = false;

    public function getAddressBalance($address, $confirmations=0)
    {
        return $this->curl('http://blockchain.info/nl/q/addressbalance/'.$address.'?confirmations='.$confirmations);
        //return $this->curl('http://blockexplorer.com/q/getreceivedbyaddress/'.$address.'/'.$confirmations);
    }

    public function getCurrentPrice()
    {
        $ticker = $this->curl('https://api.bitcoinaverage.com/ticker/global/USD/');
        //echo '<pre>'.print_r($ticker, true)."</pre>\n";

        if($this->use_24h_avg)    {
            return $ticker->{'24h_avg'};
        }   else    {
            return $ticker->last;
        }
    }

    public function getReceiveAddress($address=null)
    {
        if(!$address)   $address = SBTCP_RECEIVE_ADDR;
        $callback_url = SBTCP_CALLBACK_URL;

        $response =  $this->curl('https://blockchain.info/api/receive?method=create&address='.$address.'&callback='.$callback_url);
//echo '<pre>'.print_r($response, true)."</pre>\n";

        if($response && property_exists($response, 'input_address'))   {
            return $response->input_address;
        }   else    {
            return false;
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
        $response = json_decode($response);
error_log('curl.response: '. print_r($response,true));
        return $response;
    }
}
