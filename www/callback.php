<?php

   include('../lib/config.php');
   include('../lib/api.class.php');

//   $addr   = filter_input(INPUT_GET, "address", FILTER_SANITIZE_STRING);
//   $secret = filter_input(INPUT_GET, "secret", FILTER_SANITIZE_STRING);
//   $oid    = filter_input(INPUT_GET, "oid", FILTER_SANITIZE_STRING);
//   $value  = filter_input(INPUT_GET, "value", FILTER_SANITIZE_STRING);
//   $transaction_hash = filter_input(INPUT_GET, "transaction_hash", FILTER_SANITIZE_STRING);
//   $confirmations    = filter_input(INPUT_GET, "confirmations", FILTER_SANITIZE_STRING);

   $filters = array(
                     'addr'   => FILTER_SANITIZE_STRING,
                     'secret' => FILTER_SANITIZE_STRING,
                     'oit'    => FILTER_SANITIZE_STRING,
                     'value'  => FILTER_SANITIZE_STRING,
                     'transaction_hash'   => FILTER_SANITIZE_STRING,
                     'confirmations'      => FILTER_SANITIZE_STRING
                    );
   extract(filter_input_array(INPUT_POST, $filters));

   //- callback should error_log returning json to blockchain won't help us
   if ($_GET['test'] == true) {
      echo 'Ignoring Test Callback';
      return;
   }

   if ($addr != RECEIVE_ADDR) {
      return json_encode(array('return'=>'false', 'error'=>'Incorrect Receiving Address'));
   }

   if ($secret != BLOCKCHAIN_SECRET) {
      return json_encode(array('return'=>'false', 'error'=>'Invalid Secret'));
   }

   if ($confirmations >= MIN_CONFIRMATIONS)  {
      //Add the invoice to the database
      $result = mysql_query("replace INTO invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)");

      //Delete from pending
      mysql_query("delete from pending_invoice_payments where invoice_id = $invoice_id limit 1");

      if($result) {
         return json_encode(array('return'=>'true', 'message'=>$result));
      }
   } else {
      //Waiting for confirmations
      //create a pending payment entry
      mysql_query("replace INTO pending_invoice_payments (invoice_id, transaction_hash, value) values($invoice_id, '$transaction_hash', $value_in_btc)");

      return json_encode(array('return'=>'false', 'error'=>'Waiting for confirmations. ('.$confirmations.'/'.MIN_CONFIRMATIONS.')'));
   }
