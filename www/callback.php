<?php

   define('SBTCP_CALLBACK', true);
   include_once('../app/lib/config.inc.php');
   include_once('app/lib/main.inc.php');

/*
[23-Sep-2014 14:39:08 America/Chicago] _REQUEST: Array
(
    [anonymous] => false
    [shared] => false
    [destination_address] => 1Bpx1hsWjAQDBgc6cJ3dkGnCHqQDnvuEkD
    [confirmations] => 0
    [address] => 1Bpx1hsWjAQDBgc6cJ3dkGnCHqQDnvuEkD
    [oid] => tJMDzn
    [value] => 116295
    [input_address] => 1GTz96yCnv9Y4aeoYSj8Bs5HfkMZ5cWx8F
    [secret] => Vx0tkyGd6T3hL
    [input_transaction_hash] => 734c592b0c5cbac894751022652f7185f9580c98417f2284c7f1918773f9ac56
    [transaction_hash] => 4907d71ea7c594e619ca28b66e0da07ceac9d3ac1d4a286b2cd3f47ee6bcce58
)
*/
   $filters = array(
                     'address'   => FILTER_SANITIZE_STRING,
                     'secret' => FILTER_SANITIZE_STRING,
                     'oid'    => FILTER_SANITIZE_STRING,
                     'value'  => FILTER_SANITIZE_STRING,
                     'input_address'      => FILTER_SANITIZE_STRING,
                     'confirmations'      => FILTER_SANITIZE_STRING,
                     'transaction_hash'   => FILTER_SANITIZE_STRING,
                     'destination_address'   => FILTER_SANITIZE_STRING,
                     'input_transaction_hash'=> FILTER_SANITIZE_STRING,
                    );
   extract(filter_input_array(INPUT_GET, $filters));
   error_log('_REQUEST: '. print_r($_REQUEST,true));

   $value = round($value / 100000000, 8);


   try {
      $sql =   "INSERT INTO callbacks ".
               "( `address`, `input_address`, `secret`, `oid`, `total`, `confirmations`, ".
               "`transaction_hash`, `destination_address`, `input_transaction_hash`) ".
               "VALUES ".
               "(:address, :input_address, :secret, :oid, :total, :confirmations, ".
               ":transaction_hash, :destination_address, :input_transaction_hash)";

      $vars = array(
                     ':oid'    => $oid,
                     ':total'  => $value,
                     ':secret'   => $secret,
                     ':address'  => $address,
                     ':input_address'     => $input_address,
                     ':confirmations'     => $confirmations,
                     ':transaction_hash'  => $transaction_hash,
                     ':destination_address'  => $destination_address,
                     ':input_transaction_hash'  => $input_transaction_hash
                  );

      $qry = $db->prepare($sql);

      foreach($vars as $key => $val)  {
         $qry->bindValue($key, $val);
      }

      //error_log('callback.sql: '. print_r($sql,true));
      //error_log('callback.vars: '. print_r($vars,true));
      $qry->execute();

   }  catch (PDOException $e) {
      error_log('error: '. print_r($e->getMessage(),true));
      error_log('['.__LINE__ .'] : '.__FILE__);
      error_log('_REQUEST: '. print_r($_REQUEST,true));
      error_log('vars: '. print_r($vars,true));
      error_log('sql: '. print_r($sql,true));
   }


   if ($_GET['test'] == true) {
      echo 'Ignoring Test Callback';
      return;
   }

   $order = Helper::get_order($oid);
   $history = Helper::$api->get_address_history($input_address);

   $received_address = $history->txs[0]->out[0]->addr;
   $final_balance = $history->final_balance/100000000;
   $total_received = $history->total_received/100000000;
   $total_sent = $history->total_sent/100000000;
error_log('callback.order: '. print_r($order,true));
//error_log('callback.history: '. print_r($history,true));
error_log('callback.history.received_address: '. print_r($received_address,true));
error_log('callback.final_balance: '. print_r($final_balance,true));
error_log('callback.total_received: '. print_r($total_received,true));
error_log('callback.total_sent: '. print_r($total_sent,true));

   if ($destination_address != '' && $destination_address != SBTCP_RECEIVE_ADDR) {
      error_log('Incorrect Destination Address: '.$destination_address);
      return false;
   }

   if ($received_address != '' && $received_address != SBTCP_RECEIVE_ADDR) {
      error_log('Incorrect Receiving Address: '.$received_address);
      return false;
   }

   if ($secret != $order->secret) {
      error_log('Invalid Secret: '.$secret);
      return false;
   }

   if ($confirmations >= SBTCP_MIN_CONFIRMATIONS)  {
      error_log('Update order COMPLETE');

      if($total_sent == $total_received && $final_balance == 0 && $total_sent <= $order->total) {
         $message = Helper::complete_order($oid);
         return true;
      }
   } else {
      Helper::update_order($oid, 'status', 'CONFIRM');
      error_log('Waiting for Confirmations: '.$oid.' ('.$confirmations.'/'.SBTCP_MIN_CONFIRMATIONS.')');
      return false;
   }

   return false;

