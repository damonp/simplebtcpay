<?php
/*
    TODO
    Track response times and use fastest remote API

 */

require_once('app/lib/easybitcoin.php');
require_once('app/models/blockchain.class.php');

class CoindRPC extends API
{

   public function __construct()
   {

      $this->coind = new Bitcoin(SBTCP_RPC_USER, SBTCP_RPC_PASS, SBTCP_RPC_HOST, SBTCP_RPC_PORT);
      $this->api = new Blockchain();
      //$this->api = new Blockcypher();

   }

    public function get_address_balance($address, $confirmations=0)
    {
        try {

            //- remote api query
            return $this->api->get_address_balance($address, $confirmations);

            //- jsonrpc query
            $address_info = $this->coind->validateaddress($address);

            if($address_info['isvalid'] == 1 && $address_info['ismine'] == 1)   {
                $balance = $this->coind->getreceivedbyaddress($address, $confirmations);
            }

            if($balance != '') {
                return floatval($balance);
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
            //- remote api query
            $history =  $this->api->get_address_history($address);

            //- jsonrpc query
            //$history = $this->coind->listtransactions($address);

            error_log('history: '. print_r($history,true));

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
            $new_address = $this->coind->getnewaddress(SBTCP_RPC_ACCT);
            //error_log('new_address: '. print_r($new_address,true));

            $address_info = $this->coind->validateaddress($new_address);
            //error_log('address_info: '. print_r($address_info,true));

            //- could check output == SBTP_RECEIVE_ADDR for security
            if($address_info['isvalid'] == 1 && $address_info['ismine'] == 1)   {
                $_SESSION['sbtcp_fwd_addr'] = $new_address;
                $_SESSION['sbtcp_fwd_addr_t_stamp'] = SBTCP_GLOBAL_TIMESTAMP;
                $_SESSION['sbtcp_fwd_addr_input'] = $address;
                return $new_address;
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
            //- remote api query
            $transaction = $this->api->get_transaction($hash);

            //- jsonrpc query
            //$transaction = $this->coind->gettransaction($hash);
            return $transaction;
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }
}
