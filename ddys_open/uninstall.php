<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

DB::query("DROP TABLE IF EXISTS " . DB::table('ddys_open_cache'));
DB::query("DROP TABLE IF EXISTS " . DB::table('ddys_open_rate'));

$finish = true;

