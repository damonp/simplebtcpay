<?php

   require_once('app/models/helper.class.php');
   require_once('app/models/api.class.php');
   require_once('app/models/address_history.class.php');
   require_once('app/models/trans_ref.class.php');
   require_once('app/models/exch_rate.class.php');

   session_start();

   try {
      if(defined('SBTCP_MYSQL_HOST'))  {


      }  else  {
         $db = new PDO('sqlite:'.SBTCP_PATH.'/app/data/drkmkt.sqlite3');
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }

   } catch (PDOException $e) {
      error_log('PDOException.Message: '. print_r($e->getMessage(),true));
      error_log('PDOException.Trace: '. print_r($e->getTrace(),true));

   }

   switch(SBTCP_API_VENDOR)    {
      default:
      case('blockchain'):
         require_once('app/models/blockchain.class.php');
         $api = new Blockchain();
      break;
      case('blockcypher'):
         require_once('app/models/blockcypher.class.php');
         $api = new Blockcypher();
      break;
      case('jsonrpc'):
         require_once('app/models/jsonrpc.class.php');
         $api = new CoindRPC();
      break;
   }

   $helper = new Helper($db, $api);

   if(defined('SBTCP_CMD') && SBTCP_CMD == true)   return;
   $exch_rate = (string) new ExchRate();


