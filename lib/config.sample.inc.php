<?php

    define('SBTCP_GLOBAL_TIMESTAMP', time());
    error_reporting('E_ALL ^ E_NOTICE ^ E_DEPRECATED');

    ini_set('include_path', dirname(__FILE__).':'.ini_get('include_path'));

    define('SBTCP_RECEIVE_ADDR', '');
    define('SBTCP_MIN_CONFIRMATIONS', 1);
    define('SBTCP_BLOCKCHAIN_SECRET', '');
    define('SBTCP_EXCH_RATE_REFRESH', 60);
    define('SBTCP_EMAIL_FROM', 'simpleBTCpay <noreply@domain.com>');
    define('SBTCP_EMAIL_ADMIN', 'User Name <user@domain.com>');
    define('SBTCP_CALLBACK_URL', 'http://yourdomain.com/callback.php');

    include('helper.class.php');
    include('api.class.php');
