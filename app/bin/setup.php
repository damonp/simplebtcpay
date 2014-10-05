<?php

    define('SBTCP_CMD', true);

    $cwd = getcwd();
    chdir(dirname(__FILE__));

    include_once('../lib/config.inc.php');
    include_once('app/lib/main.inc.php');

    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);

    //- create callbacks table if necessary
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='callbacks';";
    $qry = $db->prepare($sql);
    $qry->execute();

    if(!$res = $qry->fetch()) {
        echo "Creating ".chr(27)."[01;36m"."callbacks".chr(27)."[0m table: ";

        $sql = <<< END_SQL
CREATE TABLE callbacks (
  `rowid` integer PRIMARY KEY NOT NULL,
  `address` varchar(50),
  `input_address` varchar(50),
  `secret` varchar(50),
  `oid` varchar(20),
  `total` numeric,
  `confirmations` integer,
  `last_update` varchar,
  `transaction_hash` varchar(100),
  `destination_address` varchar(100) DEFAULT(null),
  `input_transaction_hash` varchar(100)
);
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";

        echo "Creating ".chr(27)."[01;36m"."callbacks".chr(27)."[0m idx_callbacks_oid:  ";

        $sql = <<< END_SQL
CREATE  INDEX "main"."idx_callbacks_oid" ON "callbacks" ("oid" ASC);
END_SQL;

        //$qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        $qry = $db->prepare($sql);
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";

        echo "Creating ".chr(27)."[01;36m"."callbacks".chr(27)."[0m callbacks_trigger_ai:  ";

        $sql = <<< END_SQL
CREATE TRIGGER callbacks_trigger_ai AFTER INSERT ON callbacks
 BEGIN
  UPDATE callbacks SET last_update = DATETIME('NOW', 'localtime')  WHERE rowid = new.rowid;
 END;
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";

        echo "Creating ".chr(27)."[01;36m"."callbacks".chr(27)."[0m callbacks_trigger_au:  ";

        $sql = <<< END_SQL
CREATE TRIGGER callbacks_trigger_au AFTER UPDATE ON callbacks
 BEGIN
  UPDATE callbacks SET last_update = DATETIME('NOW', 'localtime')  WHERE rowid = new.rowid;
 END;
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
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

        $sql = <<< END_SQL
CREATE TABLE `orders` (
    `rowid`    INTEGER PRIMARY KEY AUTOINCREMENT,
    `oid`   NUMERIC NOT NULL UNIQUE,
    `total` NUMERIC,
    `email` varchar(100),
    `desc`  NUMERIC,
    `status`    varchar(10),
    `btc_usd`   NUMERIC,
    `tot_usd`   NUMERIC,
    `tot_drk`   NUMERIC,
    `address`   varchar(50),
    `secret`    varchar(50) NOT NULL,
    `t_stamp`   INTEGER,
    `last_update`   TEXT
);
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";

        echo "Creating ".chr(27)."[01;36m"."orders".chr(27)."[0m idx_orders_oid: ";

        $sql = <<< END_SQL
CREATE  INDEX "main"."idx_orders_oid" ON "orders" ("oid" ASC);
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";

        echo "Creating ".chr(27)."[01;36m"."orders".chr(27)."[0m orders_trigger_ai: ";

        $sql = <<< END_SQL
CREATE TRIGGER orders_trigger_ai AFTER INSERT ON orders
 BEGIN
  UPDATE orders SET last_update = DATETIME('NOW', 'localtime')  WHERE rowid = new.rowid;
 END;
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";

        echo "Creating ".chr(27)."[01;36m"."orders".chr(27)."[0m orders_trigger_au: ";

        $sql = <<< END_SQL
CREATE TRIGGER orders_trigger_au AFTER UPDATE ON orders
 BEGIN
  UPDATE orders SET last_update = DATETIME('NOW', 'localtime')  WHERE rowid = new.rowid;
 END;
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";
    }

    //- create walletnotify table if necessary
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='walletnotify';";
    $qry = $db->prepare($sql);
    $qry->execute();

    if(!$res = $qry->fetch()) {
        echo "Creating ".chr(27)."[01;36m"."walletnotify".chr(27)."[0m table: ";

        $sql = <<< END_SQL
CREATE TABLE "walletnotify" 
(

 `rowid` integer PRIMARY KEY NOT NULL,
 "txid" varchar(100) NOT NULL  UNIQUE ,
 "tot_amt" NUMERIC, "tot_fee" NUMERIC,
 "confirmations" INTEGER,
 "comment" varchar(50),
 "blocktime" varchar(20),
 "account" varchar(50),
 "address" varchar(50),
 "category" varchar(20),
 "amount" NUMERIC,
 "fee" NUMERIC,
 "last_update" VARCHAR DEFAULT CURRENT_TIMESTAMP
);
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";

        echo "Creating ".chr(27)."[01;36m"."walletnotify".chr(27)."[0m idx_walletnotify_txid:  ";

        $sql = <<< END_SQL
CREATE  INDEX "main"."idx_walletnotify_txid" ON "walletnotify" ("txid" ASC);
END_SQL;

        //$qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        $qry = $db->prepare($sql);
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";

        echo "Creating ".chr(27)."[01;36m"."walletnotify".chr(27)."[0m walletnotify_trigger_ai:  ";

        $sql = <<< END_SQL
CREATE TRIGGER walletnotify_trigger_ai AFTER INSERT ON walletnotify
 BEGIN
  UPDATE walletnotify SET last_update = DATETIME('NOW', 'localtime')  WHERE rowid = new.rowid;
 END;
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";

        echo "Creating ".chr(27)."[01;36m"."walletnotify".chr(27)."[0m walletnotify_trigger_au:  ";

        $sql = <<< END_SQL
CREATE TRIGGER walletnotify_trigger_au AFTER UPDATE ON walletnotify
 BEGIN
  UPDATE walletnotify SET last_update = DATETIME('NOW', 'localtime')  WHERE rowid = new.rowid;
 END;
END_SQL;

        $qry = $db->prepare(str_replace(array("\n", "   ", "  "), " ", $sql));
        if($qry->execute()) echo chr(27)."[01;32m"."OK".chr(27)."[0m\n";
        else    echo chr(27)."[02;31m"."FAILED".chr(27)."[0m\n";
        //echo $sql."\n";
    }

    echo chr(27)."[01;32m"."Install Complete".chr(27)."[0m\n";

    chdir($cwd);
