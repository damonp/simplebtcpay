<?php

    include('../vendor/phpqrcode/qrlib.php');

    $backColor = 0xFFFF00;
    $foreColor = 0xFF00FF;

    $filters = array(
                         'addr' => FILTER_SANITIZE_STRING,
                         'amt'  => FILTER_SANITIZE_STRING,
                         'oid'  => FILTER_SANITIZE_STRING,
                        );
    extract(filter_input_array(INPUT_GET, $filters));

    QRcode::png("bitcoin:".$addr."?amount=".$amt."&label=".$oid, false, "L", 8, 0, false, $backColor, $foreColor);
