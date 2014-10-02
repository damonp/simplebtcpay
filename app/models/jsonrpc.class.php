<?php

require_once('app/lib/easybitcoin.php');

class CoindRPC extends API
{

   public function __construct()
   {
      $this->coind = new Bitcoin(SBTCP_RPC_USER, SBTCP_RPC_PASS, SBTCP_RPC_HOST, SBTCP_RPC_PORT);
   }

    public function get_address_balance($address, $confirmations=0)
    {
        try {

            $balance = $this->coind->getbalance($address);
            //$balance = $this->curl('http://blockchain.info/nl/q/addressbalance/'.$address.'?confirmations='.$confirmations);
error_log('balance: '. print_r($balance,true));
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

    public function get_address_history($address, $vendor=null)
    {

        $vendor = $vendor ? $vendor:SBTCP_API_VENDOR;
        try {
            $history =  $this->curl('http://blockchain.info/rawaddr/'.$address);

            $addr_hist = array(
                               'address'    => $history->address,
                               'n_tx'       => $history->n_tx,
                               'total_sent' => $history->total_sent,
                               'total_received' => $history->total_received,
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

            $url_params = array(
                                'oid'       => $oid,
                                'secret'    => $secret
                                );

            $callback_url = SBTCP_CALLBACK_URL.'?'.http_build_query($url_params);
            $response =  $this->curl('https://blockchain.info/api/receive?method=create&address='.$address.'&callback='.urlencode($callback_url));
            //error_log('get_receive_address.response: '. print_r($response,true));

            //- could check output == SBTP_RECEIVE_ADDR for security
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
            $transaction = $this->curl('http://blockchain.info/rawtx/'.$hash);
            return $transaction;
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }
}
