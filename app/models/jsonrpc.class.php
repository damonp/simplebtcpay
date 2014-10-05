<?php
/*
    TODO
    Track response times and use fastest remote API

 */

require_once('app/lib/easybitcoin.php');
//require_once('app/models/blockchain.class.php');

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

            $addr_hist = array(
                               'address'    => $history->address,
                               'n_tx'       => $history->n_tx,
                               'total_sent' => $history->total_sent,
                               'total_received' => $history->total_received,
                               'final_balance'  => $history->final_balance,
                               //'txns'       => $txns
                               );
            $addr_hist = new AddressHistory($addr_hist);

            //- jsonrpc query
/*
            $address_info = $this->coind->validateaddress($address);

            if($address_info['isvalid'] == 1 && $address_info['ismine'] == 1)   {
                $history = $this->coind->listtransactions(SBTCP_RPC_ACCT);

                $txns = array();
                $final_balance = $balance = 0;
                foreach($history as $txn) {
                    if($txn['address'] != $address)    continue;
                    $n_tx = $total_received = $total_sent = 0;

                    $n_tx = intval($addr_hist['n_tx']) + 1;
                    switch($txn['category'])  {
                        case('receive'):
                            $total_received = $addr_hist['total_received'] += $txn['amount'];
                            $balance = $balance + $txn['amount'];

                            //- can we trust final balance here?  do we need more history
                            $final_balance = $final_balance + $txn['amount'];
                        break;
                        case('send'):
                            //- is amount +/- here? do we need do subtract or add negative?
                            $total_sent = $addr_hist['total_sent'] += $txn['amount'];
                            $balance = $balance - $txn['amount'];

                            //- can we trust final balance here?  do we need more history
                            $final_balance = $final_balance + $txn['amount'];
                        break;
                    }

                    $txns[] = array(
                                       'hash'   => $txn['txid'],
                                       'value'  => $txn['amount'],
                                       'spent'  => $txn['spent'],
                                       'spent_by'  => $txn['spent_by'],
                                       'confirmations'   => $txn['confirmations'],
                                       );
                }

                $addr_hist = array(
                                   'address'    => $address,
                                   'n_tx'       => $n_tx,
                                   'total_sent' => $total_sent,
                                   'total_received' => $total_received,
                                   'balance'        => $balance,
                                   'final_balance'  => $final_balance,
                                   'txns'           => $txns
                                   );

                $addr_hist = new AddressHistory($addr_hist);

            }   else {
                $addr_hist = false;
                error_log('Address invalid: '.$address);
                error_log('['.__LINE__.'] : '.__FILE__);
            }
*/

error_log('get_address_history.addr_hist: '. print_r($addr_hist,true));
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

            $new_address = $this->api->get_receive_address($address, $secret, $oid);
            //error_log('new_address: '. print_r($new_address,true));

            //- could check output == SBTP_RECEIVE_ADDR for security
            if($new_address)   {
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
            $txn = $this->api->get_transaction($hash);

            //- jsonrpc query
            //$txn = $this->coind->gettransaction($hash);
            return $txn;
        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }
}
