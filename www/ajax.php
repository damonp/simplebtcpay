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
            $total = $balance = 0;
            $balance = $api->get_address_balance($addr, SBTCP_MIN_CONFIRMATIONS);
            $history = $api->get_address_history($addr);
            $receipt_address = $history->txs[0]->out[0]->addr;
            $final_balance = $history->final_balance;
            $total_received = $history->total_received;
            $total_sent = $history->total_sent;

error_log('check_receipt.balance: '. print_r($balance,true));
error_log('check_receipt.history: '. print_r($history,true));
//error_log('check_receipt.history.addr: '. print_r($history->txs[0]->out[0]->addr,true));
error_log('receipt_address: '. print_r($receipt_address,true));

            try {

                $order = Helper::get_order($oid);
                $total = round(floatval($order->total), 8);
error_log('order: '. print_r($order,true));
            }  catch (PDOException $e) {
                error_log('error: '. print_r($e->getMessage(),true));
                error_log('FILE: '. print_r(__FILE__,true));
                error_log('LINE: '. print_r(__LINE__,true));
            }

error_log('total: '. print_r($total,true));
            if($receipt_address != SBTCP_RECEIVE_ADDR)  {
                $out = array("return"=>false,"message"=>"Transaction Not Found","history"=>$history);
                error_log('receipt_address does not match SBTCP_RECEIVE_ADDR');
                error_log('receipt_address: '. print_r($receipt_address,true));
                error_log('SBTCP_RECEIVE_ADDR: '. print_r(SBTCP_RECEIVE_ADDR,true));
            }   else if($total_sent == $total_received && $final_balance == 0 && $total_sent <= $total) {
                $message = Helper::complete_order($oid);
                $out = array("return"=>true,"balance"=>number_format($total_sent, 8),'message'=>$message);
            }   else if($balance > 0 && $balance <= $total) {
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

