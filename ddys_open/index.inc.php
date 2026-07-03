<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/ddys_open/source/bootstrap.inc.php';

$view = ddys_open_get('view', 'latest');
$params = array(
    'q' => ddys_open_get('ddys_q', ddys_open_get('q')),
    'type' => ddys_open_get('ddys_type', ddys_open_get('type', 'movie')),
    'year' => ddys_open_get('year'),
    'month' => ddys_open_get('month'),
    'slug' => ddys_open_get('slug'),
    'page' => ddys_open_get('page', 1),
    'limit' => ddys_open_get('limit', 12),
);
$titles = array(
    'latest' => '最新影片',
    'hot' => '热门影片',
    'search' => '搜索',
    'calendar' => '影片日历',
    'movie' => '影片详情',
    'collections' => '片单',
    'requests' => '求片',
);
$title = isset($titles[$view]) ? $titles[$view] : '低端影视';
$content = ddys_open_render_page($view, $params);

include template('ddys_open:page');

