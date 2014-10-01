<?php

class Blockcypher extends API
{

    public function get_address_balance($address, $confirmations=0)
    {
        try {

            $balance = $this->curl('http://api.blockcypher.com/v1/btc/main/addrs/'.$address.'?unspentOnly=true');
            if($balance->txrefs && $balance->txrefs[0]->confirmations > $confirmations) {
                return $balance->balance / 100000000;
            }   else    {
                return 0;
            }

        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_address_history($address)
    {

        try {
            $history = $this->curl('http://api.blockcypher.com/v1/btc/main/addrs/'.$address);

            $total_received = $total_sent = 0;

            //- is this a single use payment forwarding address?
            if($history->n_tx == 2 && $history->final_balance == 0)    {
                //- if yes, then total_sent/recvd should = last transaction's output value
                if(is_array($history->txrefs))  {
                    $total_received = $total_sent = $history->txrefs[0]->value;
                }
            }

            $addr_hist = array(
                               'address'    => $history->address,
                               'n_tx'       => $history->n_tx,
                               'total_sent' => $total_sent,
                               'total_received' => $total_received,
                               'final_balance'  => $history->final_balance,
                               //'txns'       => $txns
                               );

            $addr_hist = new AddressHistory($addr_hist);

            return $addr_hist;

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
            //error_log('blockcypher.callback_url: '. print_r($callback_url,true));

            $post_params = array(
                                 'token'        => SBTCP_BLOCKCYPHER_TOKEN,
                                 'destination'  => $address,
                                 'callback'     => urlencode($callback_url),
                                 );

            $response =  $this->curl('https://api.blockcypher.com/v1/btc/main/payments', $post_params);
            //error_log('get_receive_address.response: '. print_r($response,true));

            //- should check output == SBTP_RECEIVE_ADDR for security
            if($response && property_exists($response, 'input_address'))   {
                $_SESSION['sbtcp_fwd_addr'] = $response->input_address;
                $_SESSION['sbtcp_fwd_addr_t_stamp'] = SBTCP_GLOBAL_TIMESTAMP;
                $_SESSION['sbtcp_fwd_addr_input'] = $address;
                return $response->input_address;
            }   else    {
                return false;
            }

        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_transaction($hash)
    {

        try {
            $transaction = $this->curl('https://api.blockcypher.com/v1/btc/main/txs/'.$hash);

            return $transaction;
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }
}