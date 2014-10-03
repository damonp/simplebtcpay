<?php

    include_once('../app/lib/config.inc.php');

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

    include_once('app/lib/main.inc.php');

    switch($act)  {
        case('balance'):
            $balance = $api->get_address_balance($addr, SBTCP_MIN_CONFIRMATIONS);
            $out = array("return"=>true,"balance"=>number_format($balance, 8));
        break;
        case('check_receipt'):
            $total = $balance = $total_sent = $total_received = $n_tx = 0;

            $balance = $api->get_address_balance($addr, SBTCP_MIN_CONFIRMATIONS);
            $history = $api->get_address_history($addr);

            $n_tx = $history->n_tx;
            $balance = $history->balance;
            $final_balance = $history->final_balance;
            $receipt_address = $history->address;
            $total_received = $history->total_received;
            $total_sent = $history->total_sent;

            /*
            error_log('check_receipt.balance: '. print_r($balance,true));
            error_log('check_receipt.total_received: '. print_r($total_received,true));
            error_log('check_receipt.total_sent: '. print_r($total_sent,true));
            error_log('check_receipt.history: '. print_r($history,true));
            error_log('check_receipt.receipt_address: '. print_r($receipt_address,true));
            */

            $order = Helper::get_order($oid);
            $total = round(floatval($order->total), 8);

            //error_log('order: '. print_r($order,true));
            //error_log('total: '. print_r($total,true));

            if($receipt_address != SBTCP_RECEIVE_ADDR && $receipt_address != $order->address)  {

                $out = array("return"=>false,"message"=>"Transaction Not Found","history"=>$history);
                error_log('receipt_address does not match SBTCP_RECEIVE_ADDR');
                error_log('receipt_address: '.$receipt_address);
                error_log('SBTCP_RECEIVE_ADDR: '.SBTCP_RECEIVE_ADDR);

            }   else if($n_tx == 2 && $total_sent == $total_received && $final_balance == 0 && $total_sent >= $total && $total_sent > 0) {

                $message = Helper::complete_order($oid);
                $out = array("return"=>true,"balance"=>number_format($total_sent, 8),'message'=>$message);

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
        case('receive_address'):
            $data = $api->get_receive_address($addr);
            $out = array("return"=>true,"data"=>$data);
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

