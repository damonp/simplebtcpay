<?php

   define('SBTCP_GLOBAL_TIMESTAMP', time());
   error_reporting('E_ALL ^ E_NOTICE ^ E_DEPRECATED');

   define('SBTCP_PATH', dirname(dirname(__DIR__)));
   ini_set('include_path', SBTCP_PATH.':'.ini_get('include_path'));

   define('SBTCP_RECEIVE_ADDR', '');
   define('SBTCP_MIN_CONFIRMATIONS', 1);
   define('SBTCP_EMAIL_FROM', 'simpleBTCpay <noreply@domain.com>');
   define('SBTCP_EMAIL_ADMIN', 'User Name <user@domain.com>');
   //define('SBTCP_SMS_ADMIN', '##########@carrier.com'); //- @txt.att.net @vtext.com etc.
   define('SBTCP_CALLBACK_URL', 'http://yourdomain.com/callback.php');

   define('SBTCP_BLOCKCYPHER_TOKEN', '');  //- see: http://blockcypher.com
   define('SBTCP_API_VENDOR', 'blockchain'); //- blockchain or blockcypher
   define('SBTCP_API_VENDOR_EXCH_RATE', 'bitcoinaverage'); //- bitcoinaverage / blockchain / coindesk / coinbase

