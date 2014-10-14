<?php

require_once('app/models/api.class.php');
require_once('app/lib/block_io.php');

class Block_Io extends API
{
    public $blockio = null;
    public static $version = '2';

    public function __construct()
    {
        $this->blockio = new BlockIo(SBTCP_BLOCKIO_KEY, SBTCP_BLOCKIO_PIN, '2');
    }

    public function get_address_balance($address, $confirmations=0)
    {
        try {

            $balance = $this->blockio->get_address_balance(array('addresses'=>$address));
            return $balance->data->available_balance;

        } catch (Exception $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__.'] : '.__FILE__);
        }
    }

    public function get_address_history($address)
    {

        try {
            $balance = $this->blockio->get_address_balance(array('addresses'=>$address));
            $history_in  = $this->blockio->get_transactions(array('type'=>'received','addresses'=>$address));
            $history_out = $this->blockio->get_transactions(array('type'=>'sent','addresses'=>$address));

            $addr_hist = array(
                               'address'    => $address,
                               'n_tx'       => 0,
                               'total_sent' => 0,
                               'total_received' => 0,
                               'final_balance'  => 0
                               );

            if(is_array($history_in->data->txs))    {
                $txns = array();
                $final_balance = $balance = $n_tx = $total_received = $total_sent = 0;
                $history_full = array('receive' => $history_in, 'send' => $history_out);

                foreach($history_full as $side => $history)   {
                    foreach($history->data->txs as $txn) {

                        //if($txn->amounts_received[0]->recipient != $address)    continue;

                        $addr_hist['n_tx'] = intval($addr_hist['n_tx']) + 1;
                        switch($side)  {
                            case('receive'):
                                $amount = $txn->amounts_received[0]->amount;
                                $addr_hist['total_received'] += $amount;
                                $addr_hist['balance'] += $amount;

                                //- can we trust final balance here?  do we need more history
                                $addr_hist['final_balance'] += $amount;
                            break;
                            case('send'):
                                $addr_hist['total_received'] += $amount;
                                $addr_hist['balance'] += $amount;

                                //- can we trust final balance here?  do we need more history
                                $addr_hist['final_balance'] += $amount;
                            break;
                        }

                        $txns[] = array(
                                        'hash'   => $txn->txid,
                                        'value'  => $amount,
                                        'confirmations'   => $txn->confirmations,
                                        );
                    }
                }

                $addr_hist['txns'] = $txns;

                $addr_hist = new AddressHistory($addr_hist);

            }   else {
                $addr_hist = false;
                error_log('Address invalid: '.$address);
                error_log('['.__LINE__.'] : '.__FILE__);
            }

error_log('addr_hist: '. print_r($addr_hist,true));
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

            $response = $this->blockio->get_new_address(array('label' => $secret));

            error_log('get_receive_address.response: '. print_r($response,true));

            //- could check output == SBTP_RECEIVE_ADDR for security
            if($response && property_exists($response->data, 'address'))   {
                $_SESSION['sbtcp_fwd_addr'] = $response->data->address;
                $_SESSION['sbtcp_fwd_addr_t_stamp'] = SBTCP_GLOBAL_TIMESTAMP;
                $_SESSION['sbtcp_fwd_addr_input'] = $address;
                return $response->data->address;
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
