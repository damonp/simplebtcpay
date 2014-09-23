<?php

    session_start();
    //session_unset();

    $db = new PDO('sqlite:../data/simplebtcpay.sqlite3') or die("Open DB FAILED!");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $api = new API();

    $helper = new Helper($db, $api);

    $exch_rate = $api->getCurrentPrice();
    //error_log('exch_rate: '. print_r($exch_rate,true));
