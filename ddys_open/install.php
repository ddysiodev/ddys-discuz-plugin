<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

DB::query("CREATE TABLE IF NOT EXISTS " . DB::table('ddys_open_cache') . " (
  cache_key varchar(190) NOT NULL,
  cache_value mediumtext NOT NULL,
  expire_at int(10) unsigned NOT NULL DEFAULT '0',
  updated_at int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cache_key),
  KEY expire_at (expire_at)
) ENGINE=MyISAM");

DB::query("CREATE TABLE IF NOT EXISTS " . DB::table('ddys_open_rate') . " (
  rate_key varchar(190) NOT NULL,
  scope varchar(32) NOT NULL DEFAULT '',
  touched_at int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (rate_key),
  KEY scope (scope),
  KEY touched_at (touched_at)
) ENGINE=MyISAM");

$finish = true;

