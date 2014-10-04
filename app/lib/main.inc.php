<?php

   require_once('app/models/helper.class.php');
   require_once('app/models/api.class.php');
   require_once('app/models/address_history.class.php');
   require_once('app/models/trans_ref.class.php');
   //require_once('app/models/exch_rate.class.php');
   require_once('app/models/exch_rate.drk.class.php');

   session_start();

   $db = new PDO('sqlite:'.SBTCP_PATH.'/app/data/drkmkt.sqlite3') or die("Open DB FAILED!");
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   switch(SBTCP_API_VENDOR)    {
      default:
      case('jsonrpc'):
         require_once('app/models/jsonrpc.class.php');
         $api = new CoindRPC();
      break;
   }

   $helper = new Helper($db, $api);

   //if(!defined('SBTCP_CMD') && SBTCP_CMD != true)  {
      $exch_rate = (string) new ExchRate();
   //}


