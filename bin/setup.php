<?php

    $cwd = getcwd();
    chdir(dirname(__FILE__));

    include('../lib/config.inc.php');

    include('main.inc.php');

    //- create callback table if necessary
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='callback';";
    $qry = $db->prepare($sql);
    $qry->execute();

    if(!$res = $qry->fetch()) {
        echo "Creating ".chr(27)."[01;36m"."callback".chr(27)."[0m table: ";

        $sql = <<< END
CREATE TABLE `callback` (
    `id`    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    `address`   TEXT,
    `secret`    TEXT,
    `oid`   TEXT,
    `transaction_hash`  TEXT,
    `value` NUMERIC,
    `confirmations` INTEGER,
    `t_stamp`   INTEGER
);
END;

        $qry = $db->prepare($sql);
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";
    }

    //- create orders table if necessary
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='orders';";
    $qry = $db->prepare($sql);
    $qry->execute();

    if(!$res = $qry->fetch()) {
        echo "Creating ".chr(27)."[01;36m"."orders".chr(27)."[0m table: ";

        $sql = <<< END
CREATE TABLE `orders` (
    `id`    INTEGER PRIMARY KEY AUTOINCREMENT,
    `oid`   NUMERIC NOT NULL UNIQUE,
    `total` NUMERIC,
    `email` TEXT,
    `desc`  NUMERIC,
    `status`    TEXT,
    `btc_usd`   NUMERIC,
    `tot_usd`   NUMERIC,
    `tot_btc`   NUMERIC,
    `address`   TEXT,
    `secret`    TEXT NOT NULL,
    `t_stamp`   INTEGER
);
END;

        $qry = $db->prepare($sql);
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";
    }

    echo chr(27)."[01;32m"."Install Complete".chr(27)."[0m\n";

    chdir($cwd);
