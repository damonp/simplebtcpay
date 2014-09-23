<?php

    function complete_order($oid)
    {
        global $db;

        //- processes to run once payment is received
        //- updated DB, download link, send email etc.
        $message = ('<a href="http://simplebtcpay.com/download.php">Download File</a>');

        try {
            $sql = "UPDATE orders SET `status` = 'COMPLETE' WHERE `oid` = :oid";
            $qry = $db->prepare($sql);
            $qry->bindValue(':oid', $oid);
            $qry->execute();
        }  catch (PDOException $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('_REQUEST: '. print_r($_REQUEST,true));
            error_log('oid: '. print_r($oid,true));
            error_log('FILE: '. print_r(__FILE__,true));
            error_log('LINE: '. print_r(__LINE__,true));
        }

        return $message;
    }

    function rand_id($length=6)
    {

        $rand_id = crypt(uniqid(rand(), 1));
        $rand_id = strip_tags(stripslashes($rand_id));
        $rand_id = str_replace(".", "", $rand_id);
        $rand_id = strrev(str_replace("/", "", $rand_id));

        return substr($rand_id, 0, $length);
    }

