<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('DDYS_OPEN_ID', 'ddys_open');
define('DDYS_OPEN_VERSION', '0.1.2');
define('DDYS_OPEN_API_DEFAULT', 'https://ddys.io/api/v1');
define('DDYS_OPEN_SITE_DEFAULT', 'https://ddys.io');

function ddys_open_defaults()
{
    return array(
        'api_base_url' => DDYS_OPEN_API_DEFAULT,
        'site_base_url' => DDYS_OPEN_SITE_DEFAULT,
        'api_key' => '',
        'timeout' => 12,
        'default_cache_ttl' => 300,
        'dictionary_cache_ttl' => 86400,
        'fresh_cache_ttl' => 300,
        'list_cache_ttl' => 600,
        'detail_cache_ttl' => 1800,
        'community_cache_ttl' => 120,
        'theme' => 'auto',
        'layout' => 'grid',
        'columns' => 4,
        'target' => '_blank',
        'show_source_link' => 1,
        'enable_styles' => 1,
        'enable_request_form' => 0,
        'request_interval' => 60,
        'show_nav' => 1,
        'enable_pretty_urls' => 0,
        'pretty_base_path' => 'ddys',
        'index_widget_shortcode' => '[ddys_latest limit="8"]',
        'forumdisplay_widget_shortcode' => '',
        'viewthread_widget_shortcode' => '',
        'debug' => 0,
    );
}

function ddys_open_settings()
{
    global $_G;
    if (empty($_G['cache']['plugin'])) {
        if (function_exists('loadcache')) {
            loadcache('plugin');
        }
    }
    $plugin = array();
    if (isset($_G['cache']['plugin'][DDYS_OPEN_ID]) && is_array($_G['cache']['plugin'][DDYS_OPEN_ID])) {
        $plugin = $_G['cache']['plugin'][DDYS_OPEN_ID];
    }
    $settings = array_merge(ddys_open_defaults(), $plugin);
    $settings['api_base_url'] = ddys_open_normalize_base_url($settings['api_base_url'], DDYS_OPEN_API_DEFAULT);
    $settings['site_base_url'] = ddys_open_normalize_base_url($settings['site_base_url'], DDYS_OPEN_SITE_DEFAULT);
    $settings['timeout'] = ddys_open_int_range($settings['timeout'], 12, 1, 30);
    $settings['default_cache_ttl'] = ddys_open_int_range($settings['default_cache_ttl'], 300, 0, 604800);
    $settings['dictionary_cache_ttl'] = ddys_open_int_range($settings['dictionary_cache_ttl'], 86400, 0, 604800);
    $settings['fresh_cache_ttl'] = ddys_open_int_range($settings['fresh_cache_ttl'], 300, 0, 604800);
    $settings['list_cache_ttl'] = ddys_open_int_range($settings['list_cache_ttl'], 600, 0, 604800);
    $settings['detail_cache_ttl'] = ddys_open_int_range($settings['detail_cache_ttl'], 1800, 0, 604800);
    $settings['community_cache_ttl'] = ddys_open_int_range($settings['community_cache_ttl'], 120, 0, 604800);
    $settings['columns'] = ddys_open_int_range($settings['columns'], 4, 1, 6);
    $settings['request_interval'] = ddys_open_int_range($settings['request_interval'], 60, 10, 3600);
    $settings['theme'] = ddys_open_choice($settings['theme'], array('auto', 'light', 'dark'), 'auto');
    $settings['layout'] = ddys_open_choice($settings['layout'], array('grid', 'list', 'compact'), 'grid');
    $settings['target'] = ddys_open_choice($settings['target'], array('_blank', '_self'), '_blank');
    foreach (array('show_source_link', 'enable_styles', 'enable_request_form', 'show_nav', 'enable_pretty_urls', 'debug') as $key) {
        $settings[$key] = ddys_open_bool($settings[$key]) ? 1 : 0;
    }
    $settings['pretty_base_path'] = ddys_open_normalize_base_path($settings['pretty_base_path'], 'ddys');
    $settings['api_key'] = trim((string)$settings['api_key']);
    return $settings;
}

function ddys_open_get($key, $default = '')
{
    return isset($_GET[$key]) ? ddys_open_request_scalar($_GET[$key], $default) : $default;
}

function ddys_open_post($key, $default = '')
{
    return isset($_POST[$key]) ? ddys_open_request_scalar($_POST[$key], $default) : $default;
}

function ddys_open_request_scalar($value, $default = '')
{
    if (is_array($value) || is_object($value)) {
        return $default;
    }
    return trim(str_replace("\0", '', (string)$value));
}

function ddys_open_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function ddys_open_substr($value, $start, $length)
{
    $value = (string)$value;
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }
    return substr($value, $start, $length);
}

function ddys_open_attr($value)
{
    return ddys_open_h($value);
}

function ddys_open_bool($value)
{
    if (is_bool($value)) {
        return $value;
    }
    $value = strtolower(trim((string)$value));
    return in_array($value, array('1', 'true', 'yes', 'on'), true);
}

function ddys_open_int_range($value, $fallback, $min, $max)
{
    if (is_numeric($value)) {
        $value = (int)$value;
        if ($value < $min) {
            return $min;
        }
        if ($value > $max) {
            return $max;
        }
        return $value;
    }
    return $fallback;
}

function ddys_open_choice($value, $allowed, $fallback)
{
    $value = strtolower(trim((string)$value));
    return in_array($value, $allowed, true) ? $value : $fallback;
}

function ddys_open_normalize_base_url($value, $fallback)
{
    $value = trim((string)$value);
    if ($value === '' || !preg_match('#^https?://#i', $value)) {
        return $fallback;
    }
    $parts = parse_url($value);
    if (empty($parts['scheme']) || empty($parts['host']) || !empty($parts['user']) || !empty($parts['pass'])) {
        return $fallback;
    }
    return rtrim($value, '/');
}

function ddys_open_safe_media_url($value)
{
    $value = trim((string)$value);
    if ($value === '' || !preg_match('#^https?://#i', $value)) {
        return '';
    }
    return $value;
}

function ddys_open_normalize_base_path($value, $fallback)
{
    $value = trim((string)$value);
    $value = trim($value, "/ \t\r\n");
    if ($value === '' || !preg_match('#^[a-zA-Z0-9_\-/]+$#', $value) || strpos($value, '..') !== false) {
        return $fallback;
    }
    return $value;
}

function ddys_open_site_url_base()
{
    global $_G;
    return isset($_G['siteurl']) ? rtrim($_G['siteurl'], '/') . '/' : '';
}

function ddys_open_endpoint_url($endpoint)
{
    $settings = ddys_open_settings();
    if (!empty($settings['enable_pretty_urls'])) {
        $base = ddys_open_site_url_base() . $settings['pretty_base_path'];
        if ($endpoint === 'request') {
            return $base . '/request-submit';
        }
    }
    return 'plugin.php?id=ddys_open:' . $endpoint;
}

function ddys_open_page_url($view = 'latest', $params = array())
{
    $settings = ddys_open_settings();
    $view = ddys_open_choice($view, array('latest', 'hot', 'search', 'calendar', 'movie', 'collections', 'requests'), 'latest');
    if (!empty($settings['enable_pretty_urls'])) {
        $base = ddys_open_site_url_base() . $settings['pretty_base_path'];
        if ($view === 'latest') {
            $url = $base . '/';
        } elseif ($view === 'movie') {
            $slug = isset($params['slug']) ? rawurlencode(ddys_open_request_scalar($params['slug'])) : '';
            $url = $slug !== '' ? $base . '/movie/' . $slug : $base . '/';
            unset($params['slug']);
        } else {
            $url = $base . '/' . rawurlencode($view);
        }
        return ddys_open_append_query($url, $params);
    }
    $query = array_merge($view === 'latest' ? array() : array('view' => $view), $params);
    return ddys_open_append_query('plugin.php?id=ddys_open:index', $query);
}

function ddys_open_append_query($url, $params)
{
    $query = ddys_open_clean_query($params);
    if (empty($query)) {
        return $url;
    }
    return $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($query, '', '&');
}

function ddys_open_clean_query($params)
{
    $out = array();
    foreach ($params as $key => $value) {
        $value = ddys_open_request_scalar($value);
        if ($value !== '') {
            $out[$key] = $value;
        }
    }
    return $out;
}

function ddys_open_plugin_url()
{
    global $_G;
    $site = isset($_G['siteurl']) ? $_G['siteurl'] : '';
    return $site . 'source/plugin/' . DDYS_OPEN_ID . '/';
}

function ddys_open_formhash()
{
    global $_G;
    return isset($_G['formhash']) ? $_G['formhash'] : '';
}

function ddys_open_check_formhash()
{
    $hash = ddys_open_post('formhash');
    return $hash !== '' && $hash === ddys_open_formhash();
}

function ddys_open_json_response($payload, $status = 200)
{
    if ($status === 200 && ddys_open_is_error($payload) && !empty($payload['status'])) {
        $status = ddys_open_int_range($payload['status'], 500, 400, 599);
    }
    if (!headers_sent()) {
        if (function_exists('http_response_code')) {
            http_response_code($status);
        }
        if (function_exists('dheader')) {
            dheader('Content-Type: application/json; charset=utf-8');
        } else {
            header('Content-Type: application/json; charset=utf-8', true, $status);
        }
    }
    echo json_encode($payload, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    exit;
}

function ddys_open_error($message, $status = 0, $payload = array())
{
    return array(
        'ddys_error' => true,
        'success' => false,
        'message' => (string)$message,
        'status' => (int)$status,
        'payload' => $payload,
    );
}

function ddys_open_is_error($value)
{
    return is_array($value) && !empty($value['ddys_error']);
}

function ddys_open_build_query($source, $keys)
{
    $out = array();
    foreach ($keys as $key) {
        if (isset($source[$key]) && trim((string)$source[$key]) !== '') {
            $out[$key] = ddys_open_normalize_query_value($key, $source[$key]);
        }
    }
    if (isset($out['perPage']) && !isset($out['per_page'])) {
        $out['per_page'] = $out['perPage'];
        unset($out['perPage']);
    }
    return $out;
}

function ddys_open_normalize_query_value($key, $value)
{
    $value = ddys_open_request_scalar($value);
    if ($value === '') {
        return '';
    }
    if ($key === 'limit' || $key === 'per_page') {
        return ddys_open_int_range($value, 12, 1, 50);
    }
    if ($key === 'page') {
        return ddys_open_int_range($value, 1, 1, 999);
    }
    if ($key === 'year') {
        return ddys_open_int_range($value, 0, 0, 2099);
    }
    if ($key === 'month') {
        return ddys_open_int_range($value, 0, 0, 12);
    }
    return $value;
}
