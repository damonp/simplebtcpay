<?php
/*
    darkcoin.conf:
        walletnotify=/usr/bin/php -f /srv/drkmkt/app/bin/walletnotify.php %s

 */
    define('SBTCP_CMD', true);

    $cwd = getcwd();
    chdir(dirname(__FILE__));

    include_once('../lib/config.inc.php');
    include_once('app/lib/main.inc.php');

    if(2==$argc)    {
        $walletinfo = $api->coind->getinfo();
        $txninfo = $api->coind->gettransaction($argv[1]);

        error_log('=== WALLETNOTIFY ===');
        error_log('walletinfo: '. print_r($walletinfo,true));
        error_log('txninfo: '. print_r($txninfo,true));
/*
(
    [amount] => -0.54199036
    [fee] => -0.001
    [confirmations] => 365
    [blockhash] => 00000000000af9b3954d9f4911fa2b181336e42c78a056582eb4063751139b6a
    [blockindex] => 1
    [blocktime] => 1412376672
    [txid] => 77a8ca4f9acad498b236c5407c53b0310311d36e96e0c90eeb6bf5f67893d4e4
    [time] => 1412376632
    [timereceived] => 1412376632
    [comment] => fwd
    [details] => Array
        (
            [0] => Array
                (
                    [account] =>
                    [address] => Xc8S9RsKDj2WvB8UZQa37Qsg1dMX25JNFu
                    [category] => send
                    [amount] => -0.54199036
                    [fee] => -0.001
                )

        )

)
(
    [amount] => 0.01
    [confirmations] => 1
    [blockhash] => 0000000000069211cd74b2eb94cb5fc6241dcc99598069bc42c9cf05a0edf38a
    [blockindex] => 2
    [blocktime] => 1412436622
    [txid] => 2d89f855191948b3338dbdf43d2983071293e0f1b48dd23b6f613b3974369d32
    [time] => 1412436476
    [timereceived] => 1412436476
    [details] => Array
        (
            [0] => Array
                (
                    [account] => fwd
                    [address] => XhwDHy9EwRKj5rHy1bt222rPGZNDdaJ2QE
                    [category] => receive
                    [amount] => 0.01
                )

        )

)
*/
        try {
            //- Keep this flat for now.  No need to join for data always queried together.
            $sql =  "INSERT INTO walletnotify ".
                    "(`txid`, `tot_amt`, `tot_fee`, `confirmations`, `comment`, `blocktime`, ".
                    "`address`, `account`, `category`, `amount`, `fee`) ".
                    "VALUES ".
                    "(:txid, :tot_amt, :tot_fee, :confirmations, :comment, :blocktime, ".
                    ":address, :account, :category, :amount, :fee)";

            $qry = $db->prepare($sql);
            error_log('walletnotify.sql: '. print_r($sql,true));

            foreach($txninfo['details'] as $id => $details) {
                $vars = array(
                                ':txid'     => $txninfo['txid'],
                                ':tot_amt'  => $txninfo['amount'],
                                ':tot_fee'  => $txninfo['fee'],
                                ':confirmations'=> $txninfo['confirmations'],
                                ':comment'  => $txninfo['comment'],
                                ':blocktime'=> $txninfo['blocktime'],
                                ':acount'   => $details['account'],
                                ':address'  => $details['address'],
                                ':category' => $details['category'],
                                ':amount'   => $details['amount'],
                                ':fee'      => $details['fee']
                            );
                if(!$txnhead)   $txnhead = $vars;

                foreach($vars as $key => $val)  {
                    $qry->bindValue($key, $val);
                }

                error_log('walletnotify.vars: '. print_r($vars,true));
                $qry->execute();
            }

            Helper::walletnotify_email($txnhead);

        }  catch (PDOException $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__ .'] : '.__FILE__);
            error_log('_REQUEST: '. print_r($_REQUEST,true));
            error_log('vars: '. print_r($vars,true));
            error_log('sql: '. print_r($sql,true));
        }
    }


    error_log('=== END WALLETNOTIFY ===');
    echo chr(27)."[01;32m"."walletnotify Complete".chr(27)."[0m\n";

    chdir($cwd);
