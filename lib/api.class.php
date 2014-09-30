<?php

class API {

    public $use_avg = false;

    public function get_address_balance($address, $confirmations=0)
    {
        try {
            switch(SBTCP_API_VENDOR) {
                default:
                case('blockchain'):
                    $balance = $this->curl('http://blockchain.info/nl/q/addressbalance/'.$address.'?confirmations='.$confirmations);
                    //$balance = $this->curl('http://blockexplorer.com/q/getreceivedbyaddress/'.$address.'/'.$confirmations);
                    return $balance / 100000000;
                break;
                case('blockcypher'):
                    $balance = $this->curl('http://api.blockcypher.com/v1/btc/main/addrs/'.$address.'?unspentOnly=true');
                    if($balance->txrefs && $balance->txrefs[0]->confirmations > $confirmations) {
                        return $balance->balance / 100000000;
                    }   else    {
                        return 0;
                    }
                break;
            }

        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_address_history($address, $vendor=null)
    {
        //- TODO need to massage data and return _our_ history object
        $vendor = $vendor ? $vendor:SBTCP_API_VENDOR;
        try {
            switch($vendor) {
                default:
                case('blockchain'):
                    return $this->curl('http://blockchain.info/rawaddr/'.$address);
                break;
                case('blockcypher'):
                    return $this->curl('http://api.blockcypher.com/v1/btc/main/addrs/'.$address);
                break;
            }
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_current_price($vendor=null)
    {
        $vendor = $vendor ? $vendor:SBTCP_API_VENDOR_EXCH_RATE;
        try {
            switch($vendor) {
                default:
                case('bitcoinaverage'):
                    $ticker = $this->curl('https://api.bitcoinaverage.com/ticker/global/USD/');
                    if($this->use_avg)    {
                        $ticker = $ticker->{'24h_avg'};
                    }   else    {
                        $ticker = $ticker->last;
                    }
                break;
                case('blockchain'):
                    $ticker = $this->curl('https://blockchain.info/ticker');
                    if($this->use_avg)    {
                        $ticker = $ticker->USD->{'15m'};
                    }   else    {
                        $ticker = $ticker->USD->last;
                    }
                break;
                case('coinbase'):
                    $ticker = $this->curl('https://coinbase.com/api/v1/prices/sell');
                    $ticker = $ticker->amount;
                break;
                case('coindesk'):
                    $ticker = $this->curl('http://api.coindesk.com/v1/bpi/currentprice.json');
                    $ticker = $ticker->bpi->USD->rate_float;
                break;
            }

            return $ticker;
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_receive_address($address=null, $secret=null, $oid=null)
    {
        if(!$address)   $address = SBTCP_RECEIVE_ADDR;

        if(array_key_exists('sbtcp_fwd_addr_t_stamp', $_SESSION) && 
                $_SESSION['sbtcp_fwd_addr_t_stamp'] > (SBTCP_GLOBAL_TIMESTAMP - 600))  {
            return $_SESSION['sbtcp_fwd_addr'];
        }

        try {

            switch(SBTCP_API_VENDOR) {
                default:
                case('blockchain'):
                    $url_params = array(
                                        'oid'       => $oid,
                                        'secret'    => $secret
                                        );
                    $callback_url = SBTCP_CALLBACK_URL.'?'.http_build_query($url_params);
                    $response =  $this->curl('https://blockchain.info/api/receive?method=create&address='.$address.'&callback='.urlencode($callback_url));
                    //echo '<pre>'.print_r($response, true)."</pre>\n";
                    error_log('get_receive_address.response: '. print_r($response,true));

                    //- could check output == SBTP_RECEIVE_ADDR for security
                    if($response && property_exists($response, 'input_address'))   {
                        $_SESSION['sbtcp_fwd_addr'] = $response->input_address;
                        $_SESSION['sbtcp_fwd_addr_t_stamp'] = SBTCP_GLOBAL_TIMESTAMP;
                        $_SESSION['sbtcp_fwd_addr_input'] = $address;
                        return $response->input_address;
                    }   else    {
                        return false;
                    }
                break;
                case('blockcypher'):
                /*
                    {
                      "input_address": "16uKw7GsQSzfMaVTcT7tpFQkd7Rh9qcXWX",
                      "destination": "15qx9ug952GWGTNn7Uiv6vode4RcGrRemh",
                      "callback_url": "https://my.domain.com/callbacks/payments",
                      "id": "399d0923-e920-48ee-8928-2051cbfbc369",
                      "token": "f47ac10b-58cc-4372-a567-0e02b2c3d479"
                    }
                 */
                    $url_params = array(
                                        'oid'       => $oid,
                                        'secret'    => $secret,
                                        );
                    $callback_url = SBTCP_CALLBACK_URL.'?'.http_build_query($url_params);
error_log('blockcypher.callback_url: '. print_r($callback_url,true));
                    $post_params = array(
                                         'token'        => SBTCP_BLOCKCYPHER_TOKEN,
                                         'destination'  => $address,
                                         'callback'     => urlencode($callback_url),
                                         );

                    $response =  $this->curl('https://api.blockcypher.com/v1/btc/main/payments', $post_params);
                    //echo '<pre>'.print_r($response, true)."</pre>\n";
                    error_log('get_receive_address.response: '. print_r($response,true));

                    //- should check output == SBTP_RECEIVE_ADDR for security
                    if($response && property_exists($response, 'input_address'))   {
                        $_SESSION['sbtcp_fwd_addr'] = $response->input_address;
                        $_SESSION['sbtcp_fwd_addr_t_stamp'] = SBTCP_GLOBAL_TIMESTAMP;
                        $_SESSION['sbtcp_fwd_addr_input'] = $address;
                        return $response->input_address;
                    }   else    {
                        return false;
                    }
                break;
            }
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_transaction($hash, $vendor=null)
    {
        $vendor = $vendor ? $vendor:SBTCP_API_VENDOR;
        try {
            switch($vendor) {
                default:
                case('blockchain'):
                    return $this->curl('http://blockchain.info/rawtx/'.$hash);
                break;
                case('blockcypher'):
                    return $this->curl('https://api.blockcypher.com/v1/btc/main/txs/'.$hash);
                break;
            }
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
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
