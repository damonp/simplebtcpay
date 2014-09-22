<?php

    include('../lib/config.inc.php');

    if(count($_GET) > 0)   {
        $filters = array(
                         'act'  => FILTER_SANITIZE_STRING,
                         'addr' => FILTER_SANITIZE_STRING,
                         'amt'  => FILTER_SANITIZE_STRING,
                         'hash' => FILTER_SANITIZE_STRING,
                         'oid'  => FILTER_SANITIZE_STRING,
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
            $balance = $api->getAddressBalance($addr, SBTCP_MIN_CONFIRMATIONS);
            $out = array("return"=>true,"balance"=>number_format(($balance/100000000), 8));
        break;
        case('check_receipt'):
            $balance = $api->getAddressBalance($addr, SBTCP_MIN_CONFIRMATIONS);

            $sql =  "SELECT * FROM invoices WHERE oid = :oid";
            $qry = $db->prepare($sql);
            $qry->bindValue(':oid', $oid);
            $qry->execute();
            $res = $qry->fetch(PDO::FETCH_OBJ);
            $total = round(floatval($res->total), 8);

            if(floatval($balance) <= $total) {
                $out = array("return"=>false,"message"=>"Funds Not Received");
            }   else    {
                $message = complete_order($oid);
                $out = array("return"=>true,"balance"=>number_format(($balance/100000000), 8),'message'=>$message);
            }
        break;
        case('history'):
            $data = $api->getAddressHistory($addr);
            $out = array("return"=>true,"history"=>$data);
        break;
        case('transaction'):
            $data = $api->getTransaction($hash);
            $out = array("return"=>true,"transaction"=>$data);
        break;
        default:
            $out = array("return"=>false,"message"=>"404: Not Found");
        break;
    }

    echo json_encode($out);

