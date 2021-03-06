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

        try {
            //- Keep this flat for now.  No need to join for data always queried together.
            $sql =  "REPLACE INTO walletnotify ".
                    "(`txid`, `tot_amt`, `tot_fee`, `confirmations`, `comment`, `blocktime`, ".
                    "`address`, `account`, `category`, `amount`, `fee`) ".
                    "VALUES ".
                    "(:txid, :tot_amt, :tot_fee, :confirmations, :comment, :blocktime, ".
                    ":address, :account, :category, :amount, :fee)";

            $qry = $db->prepare($sql);
            //error_log('walletnotify.sql: '. print_r($sql,true));

            foreach($txninfo['details'] as $id => $details) {
                $vars = array(
                                'txid'     => $txninfo['txid'],
                                'tot_amt'  => $txninfo['amount'],
                                'tot_fee'  => $txninfo['fee'],
                                'confirmations'=> $txninfo['confirmations'],
                                'comment'  => $txninfo['comment'],
                                'blocktime'=> $txninfo['blocktime'],
                                'account'  => $details['account'],
                                'address'  => $details['address'],
                                'category' => $details['category'],
                                'amount'   => $details['amount'],
                                'fee'      => $details['fee']
                            );
                if(!$txnhead)   $txnhead = $vars;

                foreach($vars as $key => $val)  {
                    $qry->bindValue(':'.$key, $val);
                }

                error_log('walletnotify.vars.'.$id.': '. print_r($vars,true));
                $qry->execute();
            }

        }  catch (PDOException $e) {
            error_log('error: '. print_r($e->getMessage(),true));
            error_log('['.__LINE__ .'] : '.__FILE__);
            error_log('vars: '. print_r($vars,true));
            error_log('sql: '. print_r($sql,true));
        }

        $address = $txnhead['address'];
        $order = Helper::get_order($address, 'address');

        if($order)  {
            $history = Helper::$api->get_address_history($address);
            error_log('walletnotify.order: '. print_r($order,true));
            error_log('walletnotify.history: '. print_r($history,true));

            $oid = $order->oid;
            $n_tx = $history->n_tx;
            $balance = $history->balance;
            $final_balance = $history->final_balance;
            $received_address = $history->address;
            $total_received = $history->total_received;
            $total_sent = $history->total_sent;

            /*
            error_log('walletnotify.oid: '. print_r($oid,true));
            error_log('walletnotify.n_tx: '. print_r($n_tx,true));
            error_log('walletnotify.balance: '. print_r($balance,true));
            error_log('walletnotify.total: '. print_r($total,true));
            error_log('walletnotify.total_received: '. print_r($total_received,true));
            error_log('walletnotify.total_sent: '. print_r($total_sent,true));
            error_log('walletnotify.received_address: '. print_r($received_address,true));
            */

            //$address_info = Helper::$api->coind->validateaddress($address);
            if($n_tx == 1 && $final_balance == $total_received && $total_received >= $total && $total_sent == 0) {
                //- payment received, not yet forwarded on.

                $message = Helper::complete_order($oid);

            }   else if($n_tx == 2 && $total_sent == $total_received && $final_balance == 0 && $total_sent >= $total && $total_sent > 0) {
                //- payment received, already forwarded

                $message = Helper::complete_order($oid);

            }   else    {
                Helper::walletnotify_email($txnhead);

                error_log('WalletNofify: ERROR completing order');
                error_log('oid: '. print_r($oid,true));
                error_log('n_tx: '. print_r($n_tx,true));
                error_log('balance: '. print_r($balance,true));
                error_log('total: '. print_r($total,true));
                error_log('total_received: '. print_r($total_received,true));
                error_log('total_sent: '. print_r($total_sent,true));
                error_log('received_address: '. print_r($received_address,true));
                error_log('order: '. print_r($order,true));
                error_log('history: '. print_r($history,true));
                error_log('['.__LINE__.'] : '.__FILE__);
            }
        }   else    {
            //- walletnotify but no order
            Helper::walletnotify_email($txnhead);
        }
    }

    error_log('=== END WALLETNOTIFY ===');
    //echo chr(27)."[01;32m"."WalletNofify Complete".chr(27)."[0m\n";

    chdir($cwd);
