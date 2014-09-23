<?php

class Helper {

    public static $api = null;
    public static $db = null;

    public function __construct($db, $api)
    {
        Helper::$api = $api;
        Helper::$db = $db;
    }

    public static function complete_order($oid)
    {

        $order = Helper::get_order($oid);
        error_log('complete_order.order: '. print_r($order,true));

        //- remove cached fwd_addr, oid etc.
        session_unset();

        //- processes to run once payment is received
        //- updated DB, download link, send email etc.

        $history = Helper::$api->get_address_history($order->address);
        //error_log('complete_order.history: '. print_r($history,true));

        $message = ('<a href="http://simplebtcpay.com/download.php">Download File</a>');

        Helper::update_order($oid, 'status', 'COMPLETE');

        Helper::order_email_admin($oid);
        Helper::order_email_user($oid);

        return $message;
    }


    public static function send_email($msg, $subj, $to)
    {

        $headers = 'From: '.SBTCP_EMAIL_FROM;
        return mail($to, $subj, $msg, $headers);
    }

    public static function get_order($oid)
    {
        $sql =  "SELECT * FROM orders WHERE oid = :oid";
        $qry = Helper::$db->prepare($sql);
        $qry->bindValue(':oid', $oid);
        $qry->execute();
        $res = $qry->fetch(PDO::FETCH_OBJ);
        return $res;
    }

    public static function order_email_admin($oid)
    {
        $order = Helper::get_order($oid);
        $msg = 'Order:'.print_r($order, true)."\n";
        $balance = Helper::$api->get_address_balance($order->address);
        $msg .= 'Balance:'.print_r($balance, true)."\n";
        if(defined('SBTCP_CALLBACK'))   $msg .= "\nCALLBACK = true\n";
        $res = Helper::send_email($msg, 'SBTCP:Order Completed', SBTCP_EMAIL_ADMIN);
    }

    public static function order_email_user($oid)
    {
        $order = Helper::get_order($oid);
        if(!$order->email || !filter_var($order->email, FILTER_VALIDATE_EMAIL))  return false;

        $msg = 'Order:'.print_r($order, true)."\n";
        $balance = Helper::$api->get_address_balance($order->address);
        $msg .= 'Balance:'.print_r($balance, true)."\n";
        $res = Helper::send_email($msg, 'Order Completed', $order->email);
    }

    public static function rand_id($length=6)
    {
        $rand_id = crypt(uniqid(rand(), 1));
        $rand_id = strip_tags(stripslashes($rand_id));
        $rand_id = str_replace(".", "", $rand_id);
        $rand_id = strrev(str_replace("/", "", $rand_id));

        return substr($rand_id, 0, $length);
    }

    public static function update_order($oid, $key, $val)
    {
        try {
            $sql = "UPDATE orders SET `".$key."` = :val WHERE `oid` = :oid";
            $qry = Helper::$db->prepare($sql);
            $qry->bindValue(':oid', $oid);
            $qry->bindValue(':val', $val);
            return $qry->execute();
        }  catch (PDOException $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
            error_log('_REQUEST: '. print_r($_REQUEST,true));
            error_log('oid: '. print_r($oid,true));
        }
    }
}
