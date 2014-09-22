<?php

    include('../lib/config.inc.php');

    if(count($_GET) > 0)   {
        $filters = array(
                         'act'  => FILTER_SANITIZE_STRING,
                         'addr' => FILTER_SANITIZE_STRING
                        );
        extract(filter_input_array(INPUT_GET, $filters));
    }

    if(false && count($_POST) > 0)   {
        $filters = array(
                         'tot_btc'  => FILTER_SANITIZE_STRING,
                         'tot_usd'  => FILTER_SANITIZE_STRING,
                         'oid'      => FILTER_SANITIZE_STRING,
                         'odesc'    => FILTER_SANITIZE_STRING,
                         'act'      => FILTER_SANITIZE_STRING,
                        );
        extract(filter_input_array(INPUT_POST, $filters));
$vars = filter_input_array(INPUT_POST, $filters);
error_log('vars.post: '. print_r($vars,true));
    }

    include('../lib/main.inc.php');

    switch($act)  {
        case('balance'):
            $balance = $api->getAddressBalance($addr);
            $out = array("return"=>true,"balance"=>$balance);
        break;
        case('check_receipt'):
            $balance = $api->getAddressBalance($addr);
            if(floatval($balance) <= 0) {
                $out = array("return"=>false,"message"=>"Funds Not Received");
            }   else    {
                $out = array("return"=>true,"balance"=>$balance);
            }
        break;
        default:
            $out = array("return"=>false,"message"=>"404: Not Found");
        break;
    }

    echo json_encode($out);

