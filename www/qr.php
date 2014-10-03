<?php

    include('../app/lib/config.inc.php');
    include('../vendor/phpqrcode/qrlib.php');

    $background = 0xFFFF00;
    $foreground = 0xFF00FF;

    $filters = array(
                         'addr' => FILTER_SANITIZE_STRING,
                         'amt'  => FILTER_SANITIZE_STRING,
                         'oid'  => FILTER_SANITIZE_STRING,
                        );
    extract(filter_input_array(INPUT_GET, $filters));

    switch(substr($addr, 0, 1))  {
        default:
        case('1'):
        case('3'):
            QRcode::png("bitcoin:".$addr."?amount=".$amt."&label=".$oid, false, "L", 8, 0, false, $background, $foreground);
        break;
        case('L'):
            QRcode::png("litecoin:".$addr, false, "L", 8, 0, false, $background, $foreground);
        break;
        case('X'):
            QRcode::png("darkcoin:".$addr, false, "L", 8, 0, false, $background, $foreground);
        break;
    }
