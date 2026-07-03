<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function ddys_open_cache_key($method, $base, $path, $params)
{
    return md5(strtoupper($method) . '|' . $base . '|' . $path . '|' . serialize($params));
}

function ddys_open_cache_get($key)
{
    if (!class_exists('DB')) {
        return false;
    }
    $row = DB::fetch_first("SELECT cache_value FROM " . DB::table('ddys_open_cache') . " WHERE cache_key='" . daddslashes($key) . "' AND expire_at>" . intval(TIMESTAMP));
    if (!$row) {
        return false;
    }
    $value = @unserialize($row['cache_value']);
    return $value === false && $row['cache_value'] !== serialize(false) ? false : $value;
}

function ddys_open_cache_set($key, $value, $ttl)
{
    if (!class_exists('DB') || $ttl <= 0) {
        return;
    }
    ddys_open_cache_prune();
    DB::insert('ddys_open_cache', array(
        'cache_key' => $key,
        'cache_value' => serialize($value),
        'expire_at' => TIMESTAMP + (int)$ttl,
        'updated_at' => TIMESTAMP,
    ), false, true);
}

function ddys_open_cache_prune()
{
    if (class_exists('DB')) {
        DB::query("DELETE FROM " . DB::table('ddys_open_cache') . " WHERE expire_at<=" . intval(TIMESTAMP));
    }
}

function ddys_open_cache_flush()
{
    if (!class_exists('DB')) {
        return 0;
    }
    DB::query("DELETE FROM " . DB::table('ddys_open_cache'));
    return DB::affected_rows();
}

function ddys_open_cache_count()
{
    if (!class_exists('DB')) {
        return 0;
    }
    $row = DB::fetch_first("SELECT COUNT(*) AS total FROM " . DB::table('ddys_open_cache'));
    return $row ? (int)$row['total'] : 0;
}

function ddys_open_check_rate_limit($scope, $key, $interval)
{
    if (!class_exists('DB') || $interval <= 0) {
        return true;
    }
    ddys_open_rate_prune($interval);
    $rateKey = md5($scope . '|' . $key);
    $row = DB::fetch_first("SELECT touched_at FROM " . DB::table('ddys_open_rate') . " WHERE rate_key='" . daddslashes($rateKey) . "'");
    if ($row && (int)$row['touched_at'] > 0 && TIMESTAMP - (int)$row['touched_at'] < $interval) {
        return false;
    }
    DB::insert('ddys_open_rate', array(
        'rate_key' => $rateKey,
        'scope' => $scope,
        'touched_at' => TIMESTAMP,
    ), false, true);
    return true;
}

function ddys_open_rate_prune($interval)
{
    if (class_exists('DB')) {
        $maxAge = max(86400, (int)$interval * 20);
        DB::query("DELETE FROM " . DB::table('ddys_open_rate') . " WHERE touched_at<" . intval(TIMESTAMP - $maxAge));
    }
}
