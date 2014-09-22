<?php

    session_start();

    $db = new PDO('sqlite:../data/simplebtcpay.sqlite3') or die("Open DB FAILED!");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $api = new API();

//    if(! array_key_exists('sbtcp_exch_rate', $_SESSION) || 
//            $_SESSION['sbtcp_exch_rate']['timestamp'] < SBTCP_GLOBAL_TIMESTAMP)  {
//
//    }

    $exch_rate = $api->getCurrentPrice();
    //error_log('exch_rate: '. print_r($exch_rate,true));
