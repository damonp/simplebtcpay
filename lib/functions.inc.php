<?php

    function rand_id($length=6)
    {
        $random = mt_rand(0, (1 << ($length << 2)) - 1);
        $number = dechex($random);
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

