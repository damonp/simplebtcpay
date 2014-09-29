<?php

    include('../lib/config.inc.php');

    if(count($_GET) > 0)   {
        $filters = array(
                         'act'  => FILTER_SANITIZE_STRING,
                         'addr' => FILTER_SANITIZE_STRING,
                         'amt'  => FILTER_SANITIZE_STRING,
                         'hash' => FILTER_SANITIZE_STRING,
                         'oid'  => FILTER_SANITIZE_STRING,
                        );
        extract(filter_input_array(INPUT_GET, $filters));
    }

    if(false && count($_POST) > 0)   {
        $filters = array(
                         'tot_btc'  => FILTER_SANITIZE_STRING,
                         'tot_usd'  => FILTER_SANITIZE_STRING,
                         'oid'      => FILTER_SANITIZE_STRING,
                         'odesc'    => FILTER_SANITIZE_STRING,
                         'act'      => FILTER_SANITIZE_STRING,
                        );
        extract(filter_input_array(INPUT_POST, $filters));
$vars = filter_input_array(INPUT_POST, $filters);
error_log('vars.post: '. print_r($vars,true));
    }

    include('main.inc.php');

    switch($act)  {
        case('balance'):
            $balance = $api->get_address_balance($addr, SBTCP_MIN_CONFIRMATIONS);
            $out = array("return"=>true,"balance"=>number_format($balance, 8));
        break;
        case('check_receipt'):
            $total = $balance = $total_sent = $total_received = 0;
            //$balance = $api->get_address_balance($addr, SBTCP_MIN_CONFIRMATIONS);
            $history = $api->get_address_history($addr);
            if($history->balance)   {
                $balance = $history->balance/100000000;
            } else  {
                $balance = 0;
            }

            if($history->final_balance)   {
                $final_balance = $history->final_balance/100000000;
            } else  {
                $final_balance = 0;
            }

            if($history->txs)   {
                $receipt_address = $history->txs[0]->out[0]->addr;
                $total_received = $history->total_received/100000000;
                $total_sent = $history->total_sent/100000000;
            }   elseif($history->txrefs)   {
                $receipt_address = $history->address;
                //$txn = $api->get_transaction($history->txrefs[0]->tx_hash);
                //error_log('txn: '. print_r($txn,true));
            }   else   {
                $receipt_address = $history->address;
            }

error_log('check_receipt.balance: '. print_r($balance,true));
error_log('check_receipt.total_received: '. print_r($total_received,true));
error_log('check_receipt.total_sent: '. print_r($total_sent,true));
error_log('check_receipt.history: '. print_r($history,true));
error_log('receipt_address: '. print_r($receipt_address,true));

            $order = Helper::get_order($oid);
            $total = round(floatval($order->total), 8);
error_log('order: '. print_r($order,true));
error_log('total: '. print_r($total,true));

            if($receipt_address != SBTCP_RECEIVE_ADDR && $receipt_address != $order->address)  {
                $out = array("return"=>false,"message"=>"Transaction Not Found","history"=>$history);
                error_log('receipt_address does not match SBTCP_RECEIVE_ADDR');
                error_log('receipt_address: '.$receipt_address);
                error_log('SBTCP_RECEIVE_ADDR: '.SBTCP_RECEIVE_ADDR);
            }   else if($total_sent == $total_received && $final_balance == 0 && $total_sent >= $total && $total_sent > 0) {
                //- blockchain forward order
                $message = Helper::complete_order($oid);
                $out = array("return"=>true,"balance"=>number_format($total_sent, 8),'message'=>$message);
            }   else if($balance == $final_balance && $final_balance >= $total && $final_balance > 0) {
                //- blockcypher forward order
                $message = Helper::complete_order($oid);
                $out = array("return"=>true,"balance"=>number_format($final_balance, 8),'message'=>$message);
            }   else if($balance > 0 && $balance >= $total) {
                $message = Helper::complete_order($oid);
                $out = array("return"=>true,"balance"=>number_format($balance, 8),'message'=>$message);
            }   else    {
                $out = array("return"=>false,"message"=>"Transaction Not Found","history"=>$history);
            }
        break;
        case('history'):
            $data = $api->get_address_history($addr);
            $out = array("return"=>true,"history"=>$data);
        break;
        case('transaction'):
            $data = $api->get_transaction($hash);
            $out = array("return"=>true,"transaction"=>$data);
        break;
        default:
            $out = array("return"=>false,"message"=>"404: Not Found");
        break;
    }

    echo json_encode($out);

