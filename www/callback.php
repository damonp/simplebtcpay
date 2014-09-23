<?php

   define('SBTCP_CALLBACK', true);
   include('../lib/config.inc.php');
   include('main.inc.php');
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

      error_log('callback.sql: '. print_r($sql,true));
      error_log('callback.vars: '. print_r($vars,true));
      $qry->execute();

   }  catch (PDOException $e) {
      error_log('error: '. print_r($e->getMessage(),true));
      error_log('FILE: '. print_r(__FILE__,true));
      error_log('LINE: '. print_r(__LINE__,true));
      error_log('_REQUEST: '. print_r($_REQUEST,true));
      error_log('vars: '. print_r($vars,true));
      error_log('sql: '. print_r($sql,true));
   }


   if ($_GET['test'] == true) {
      echo 'Ignoring Test Callback';
      return;
   }

   $order = Helper::get_order($oid);
   $history = Helper::$api->get_address_history($address);
   $received_address = $history->txs[0]->out[0]->addr;
   $final_balance = $history->final_balance;
   $total_received = $history->total_received;
   $total_sent = $history->total_sent;
error_log('callback.order: '. print_r($order,true));
error_log('callback.history: '. print_r($history,true));
error_log('callback.history.received_address: '. print_r($received_address,true));

   if ($destination_address != '' && $destination_address != SBTCP_RECEIVE_ADDR) {
      error_log('Incorrect Destination Address');
      return false;
   }

   if ($received_address != '' && $received_address != SBTCP_RECEIVE_ADDR) {
      error_log('Incorrect Receiving Address');
      return false;
   }

   if ($secret != $order->secret) {
      error_log('Invalid Secret');
      return false;
   }

   if ($confirmations >= SBTCP_MIN_CONFIRMATIONS)  {
      error_log('Update order COMPLETE');

      if($total_sent == $total_received && $final_balance == 0 && $total_sent <= $order->total) {
         $message = Helper::complete_order($oid);
         return true;
      }

      //Add the invoice to the database
      //$result = mysql_query("replace INTO order_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)");

      //Delete from pending
      //mysql_query("delete from pending_invoice_payments where invoice_id = $invoice_id limit 1");

      //if($result) {
      //   return json_encode(array('return'=>'true', 'message'=>$result));
      //}
   } else {
      //Waiting for confirmations
      //create a pending payment entry
     // mysql_query("replace INTO pending_invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)");

      //return json_encode(array('return'=>'false', 'error'=>'Waiting for confirmations. ('.$confirmations.'/'.MIN_CONFIRMATIONS.')'));
      Helper::update_order($oid, 'status', 'CONFIRM');
      error_log('Waiting for Confirmations: '.$oid.' ('.$confirmations.'/'.SBTCP_MIN_CONFIRMATIONS.')');
      return false;
   }
