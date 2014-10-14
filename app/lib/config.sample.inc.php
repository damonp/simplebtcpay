<?php

   define('SBTCP_GLOBAL_TIMESTAMP', time());
   error_reporting('E_ALL ^ E_NOTICE ^ E_DEPRECATED');

   define('SBTCP_PATH', dirname(dirname(__DIR__)));
   ini_set('include_path', SBTCP_PATH.':'.ini_get('include_path'));

   define('SBTCP_RECEIVE_ADDR', '');   //- main payment address
   define('SBTCP_MIN_CONFIRMATIONS', 1);
   define('SBTCP_EMAIL_FROM', 'simpleBTCpay <noreply@domain.com>');
   define('SBTCP_EMAIL_ADMIN', 'User Name <user@domain.com>');
   //define('SBTCP_SMS_ADMIN', '##########@carrier.com'); //- @txt.att.net @vtext.com etc.
   define('SBTCP_CALLBACK_URL', 'http://yourdomain.com/callback.php');

   define('SBTCP_API_VENDOR', 'blockchain'); //- jsonrpc, blockchain, blockcypher, blockio
   define('SBTCP_API_VENDOR_EXCH_RATE', 'bitcoinaverage'); //- bitcoinaverage / blockchain / coindesk / coinbase

   // Optional depending on API vendor

   //- blockcypher.com
   //define('SBTCP_BLOCKCYPHER_TOKEN', '');

   //- block.io
   //define('SBTCP_BLOCKIO_KEY', '');
   //define('SBTCP_BLOCKIO_PIN', '');

   //- jsonrpc
   //define('SBTCP_RPC_USER', '');
   //define('SBTCP_RPC_PASS', '');
   //define('SBTCP_RPC_HOST', '');
   //define('SBTCP_RPC_PORT', '');

