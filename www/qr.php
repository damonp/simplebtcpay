<?php

    include('../vendor/phpqrcode/qrlib.php');

    $backColor = 0xFFFF00;
    $foreColor = 0xFF00FF;

    $addr   = filter_input(INPUT_GET, "addr", FILTER_SANITIZE_STRING);
    $amt    = filter_input(INPUT_GET, "amt", FILTER_SANITIZE_STRING);
    $oid    = filter_input(INPUT_GET, "oid", FILTER_SANITIZE_STRING);
//    $addr = $_REQUEST['addr'];
//    $amt = $_REQUEST['amt'];
//    $oid = $_REQUEST['oid'];
error_log('addr: '. print_r($addr,true));
error_log('amt: '. print_r($amt,true));
error_log('oid: '. print_r($oid,true));
    QRcode::png("bitcoin:".$addr."?amount=".$amt."&label=".$oid, false, "L", 8, 0, false, $backColor, $foreColor);
