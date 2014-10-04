<?php

    define('SBTCP_CMD', true);

    $cwd = getcwd();
    chdir(dirname(__FILE__));

    include_once('../lib/config.inc.php');
    include_once('app/lib/main.inc.php');

    //- create orders table if necessary
    $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='orders';";
    $qry = $db->prepare($sql);
    $qry->execute();

    if(!$res = $qry->fetch()) {

    }


    echo chr(27)."[01;32m"."Cron Complete".chr(27)."[0m\n";

    chdir($cwd);
