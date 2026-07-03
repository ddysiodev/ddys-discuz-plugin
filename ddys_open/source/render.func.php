<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function ddys_open_payload_data($payload)
{
    if (is_array($payload) && array_key_exists('data', $payload)) {
        return $payload['data'];
    }
    return $payload;
}

function ddys_open_to_list($data)
{
    if (!is_array($data)) {
        return array();
    }
    foreach (array('items', 'movies', 'results', 'shares', 'requests', 'activities', 'comments') as $key) {
        if (isset($data[$key]) && is_array($data[$key])) {
            return $data[$key];
        }
    }
    if (ddys_open_is_assoc($data)) {
        return array($data);
    }
    return $data;
}

function ddys_open_is_assoc($array)
{
    if (!is_array($array)) {
        return false;
    }
    if (empty($array)) {
        return false;
    }
    return array_keys($array) !== range(0, count($array) - 1);
}

function ddys_open_item_value($item, $keys, $fallback = '')
{
    foreach ($keys as $key) {
        if (isset($item[$key]) && $item[$key] !== '') {
            return $item[$key];
        }
    }
    return $fallback;
}

function ddys_open_site_url($item)
{
    $settings = ddys_open_settings();
    if (isset($item['url']) && preg_match('#^https?://#i', $item['url'])) {
        return $item['url'];
    }
    if (isset($item['url']) && substr($item['url'], 0, 1) === '/') {
        return rtrim($settings['site_base_url'], '/') . $item['url'];
    }
    if (isset($item['slug']) && $item['slug'] !== '') {
        return rtrim($settings['site_base_url'], '/') . '/movie/' . rawurlencode($item['slug']);
    }
    return '';
}

function ddys_open_wrap($html, $args = array())
{
    $settings = ddys_open_settings();
    $layout = isset($args['layout']) && $args['layout'] !== '' ? $args['layout'] : $settings['layout'];
    $theme = isset($args['theme']) && $args['theme'] !== '' ? $args['theme'] : $settings['theme'];
    $layout = ddys_open_choice($layout, array('grid', 'list', 'compact'), $settings['layout']);
    $theme = ddys_open_choice($theme, array('auto', 'light', 'dark'), $settings['theme']);
    $columns = isset($args['columns']) && $args['columns'] !== '' ? (int)$args['columns'] : (int)$settings['columns'];
    $columns = ddys_open_int_range($columns, 4, 1, 6);
    return '<div class="ddys-discuz ddys-discuz-theme-' . ddys_open_attr($theme) . ' ddys-discuz-layout-' . ddys_open_attr($layout) . '" style="--ddys-discuz-columns:' . $columns . '">' . $html . '</div>';
}

function ddys_open_render_error($payload, $args = array())
{
    $message = is_array($payload) && isset($payload['message']) ? $payload['message'] : '低端影视内容加载失败。';
    return ddys_open_wrap('<div class="ddys-discuz-error">' . ddys_open_h($message) . '</div>', $args);
}

function ddys_open_render_empty($message, $args = array())
{
    return ddys_open_wrap('<div class="ddys-discuz-empty">' . ddys_open_h($message) . '</div>', $args);
}

function ddys_open_render_card($item, $settings)
{
    if (!is_array($item)) {
        return '';
    }
    $title = ddys_open_item_value($item, array('title', 'name', 'username'), 'Untitled');
    $poster = ddys_open_safe_media_url(ddys_open_item_value($item, array('poster', 'cover', 'avatar'), ''));
    $url = ddys_open_site_url($item);
    $target = $settings['target'];
    $meta = array();
    foreach (array('year', 'type', 'type_code', 'region', 'quality') as $key) {
        if (!empty($item[$key])) {
            $meta[] = $item[$key];
        }
    }
    if (!empty($item['rating'])) {
        $meta[] = '评分 ' . $item['rating'];
    }
    $summary = ddys_open_item_value($item, array('description', 'intro', 'summary', 'note', 'content'), '');
    $html = '<article class="ddys-discuz-card">';
    if ($poster !== '') {
        $html .= '<div class="ddys-discuz-poster"><img src="' . ddys_open_attr($poster) . '" alt="' . ddys_open_attr($title) . '" loading="lazy" /></div>';
    }
    $html .= '<div class="ddys-discuz-card-body">';
    $html .= '<h3 class="ddys-discuz-title">';
    if ($url !== '' && !empty($settings['show_source_link'])) {
        $html .= '<a href="' . ddys_open_attr($url) . '" target="' . ddys_open_attr($target) . '" rel="noopener">' . ddys_open_h($title) . '</a>';
    } else {
        $html .= ddys_open_h($title);
    }
    $html .= '</h3>';
    if (!empty($meta)) {
        $html .= '<div class="ddys-discuz-meta">' . ddys_open_h(implode(' / ', $meta)) . '</div>';
    }
    if ($summary !== '') {
        $html .= '<div class="ddys-discuz-summary">' . ddys_open_h(ddys_open_substr(strip_tags((string)$summary), 0, 160)) . '</div>';
    }
    $html .= '</div></article>';
    return $html;
}

function ddys_open_render_list($payload, $args = array())
{
    if (ddys_open_is_error($payload)) {
        return ddys_open_render_error($payload, $args);
    }
    $data = ddys_open_payload_data($payload);
    $items = ddys_open_to_list($data);
    if (empty($items)) {
        return ddys_open_render_empty('暂无低端影视内容。', $args);
    }
    $settings = ddys_open_settings();
    $html = '<div class="ddys-discuz-items">';
    foreach ($items as $item) {
        $html .= ddys_open_render_card($item, $settings);
    }
    $html .= '</div>';
    return ddys_open_wrap($html, $args);
}

function ddys_open_render_detail($payload, $args = array())
{
    if (ddys_open_is_error($payload)) {
        return ddys_open_render_error($payload, $args);
    }
    $data = ddys_open_payload_data($payload);
    if (!is_array($data)) {
        return ddys_open_render_empty('暂无详情。', $args);
    }
    $settings = ddys_open_settings();
    $html = '<div class="ddys-discuz-detail">';
    $html .= ddys_open_render_card($data, $settings);
    $intro = ddys_open_item_value($data, array('intro', 'description', 'summary', 'note'), '');
    if ($intro !== '') {
        $html .= '<div class="ddys-discuz-description">' . nl2br(ddys_open_h($intro)) . '</div>';
    }
    if (!empty($data['movies']) && is_array($data['movies'])) {
        $html .= '<h3>影片</h3><div class="ddys-discuz-items">';
        foreach ($data['movies'] as $item) {
            $html .= ddys_open_render_card($item, $settings);
        }
        $html .= '</div>';
    }
    if (!empty($data['resources']) || !empty($data['sources'])) {
        $html .= ddys_open_render_sources(array('data' => $data), $args, true);
    }
    $html .= '</div>';
    return ddys_open_wrap($html, $args);
}

function ddys_open_render_sources($payload, $args = array(), $inner = false)
{
    if (ddys_open_is_error($payload)) {
        return ddys_open_render_error($payload, $args);
    }
    $data = ddys_open_payload_data($payload);
    $groups = array();
    if (isset($data['resources'])) {
        $groups['资源'] = $data['resources'];
    } elseif (isset($data['sources'])) {
        $groups['资源'] = $data['sources'];
    } elseif (is_array($data)) {
        $groups = ddys_open_is_assoc($data) ? $data : array('资源' => $data);
    }
    $html = '<div class="ddys-discuz-sources">';
    foreach ($groups as $name => $resources) {
        if (!is_array($resources)) {
            continue;
        }
        $html .= '<section class="ddys-discuz-source-group"><h3>' . ddys_open_h($name) . '</h3>';
        foreach ($resources as $resource) {
            if (!is_array($resource)) {
                continue;
            }
            $title = ddys_open_item_value($resource, array('title', 'name', 'download_type', 'type'), '资源');
            $url = ddys_open_item_value($resource, array('url', 'link', 'href'), '');
            $safe = preg_match('#^(https?:|magnet:|ed2k:|thunder:)#i', $url) ? $url : '';
            $html .= '<p class="ddys-discuz-resource">';
            $html .= $safe !== '' ? '<a href="' . ddys_open_attr($safe) . '" target="_blank" rel="noopener">' . ddys_open_h($title) . '</a>' : ddys_open_h($title);
            $html .= '</p>';
        }
        $html .= '</section>';
    }
    $html .= '</div>';
    return $inner ? $html : ddys_open_wrap($html, $args);
}

function ddys_open_render_calendar($payload, $args = array())
{
    if (ddys_open_is_error($payload)) {
        return ddys_open_render_error($payload, $args);
    }
    $data = ddys_open_payload_data($payload);
    $days = isset($data['days']) ? $data['days'] : $data;
    if (!is_array($days)) {
        return ddys_open_render_list($payload, $args);
    }
    $settings = ddys_open_settings();
    $html = '<div class="ddys-discuz-calendar">';
    foreach ($days as $day => $items) {
        $html .= '<section class="ddys-discuz-calendar-day"><h3>' . ddys_open_h($day) . '</h3><div class="ddys-discuz-items">';
        if (is_array($items)) {
            foreach ($items as $item) {
                $html .= ddys_open_render_card($item, $settings);
            }
        }
        $html .= '</div></section>';
    }
    $html .= '</div>';
    return ddys_open_wrap($html, $args);
}

function ddys_open_render_dictionary($payload, $args = array())
{
    if (ddys_open_is_error($payload)) {
        return ddys_open_render_error($payload, $args);
    }
    $items = ddys_open_to_list(ddys_open_payload_data($payload));
    if (empty($items)) {
        return ddys_open_render_empty('暂无字典数据。', $args);
    }
    $html = '<div class="ddys-discuz-tags">';
    foreach ($items as $item) {
        $label = is_array($item) ? ddys_open_item_value($item, array('name', 'title', 'label', 'value'), '') : $item;
        if ($label !== '') {
            $html .= '<span>' . ddys_open_h($label) . '</span>';
        }
    }
    $html .= '</div>';
    return ddys_open_wrap($html, $args);
}

function ddys_open_render_search($args = array())
{
    $q = ddys_open_get('ddys_q', isset($args['q']) ? $args['q'] : '');
    $type = ddys_open_get('ddys_type', isset($args['type']) ? $args['type'] : 'movie');
    $settings = ddys_open_settings();
    $html = '<form class="ddys-discuz-search" method="get" action="' . ddys_open_attr(!empty($settings['enable_pretty_urls']) ? ddys_open_page_url('search') : 'plugin.php') . '">';
    if (empty($settings['enable_pretty_urls'])) {
        $html .= '<input type="hidden" name="id" value="ddys_open:index" />';
        $html .= '<input type="hidden" name="view" value="search" />';
    }
    $html .= '<input type="search" name="ddys_q" value="' . ddys_open_attr($q) . '" placeholder="搜索低端影视" />';
    $html .= '<select name="ddys_type"><option value="movie"' . ($type === 'movie' ? ' selected' : '') . '>影片</option><option value="share"' . ($type === 'share' ? ' selected' : '') . '>分享</option><option value="request"' . ($type === 'request' ? ' selected' : '') . '>求片</option></select>';
    $html .= '<button type="submit">搜索</button></form>';
    if ($q !== '') {
        $payload = ddys_open_api_get('/search', array('q' => $q, 'type' => $type, 'per_page' => isset($args['per_page']) ? $args['per_page'] : 12), array());
        $html .= ddys_open_render_list($payload, $args);
    }
    return ddys_open_wrap($html, $args);
}

function ddys_open_render_request_form($args = array())
{
    $settings = ddys_open_settings();
    if (empty($settings['enable_request_form'])) {
        return ddys_open_render_empty('求片表单未启用。', $args);
    }
    $action = ddys_open_endpoint_url('request');
    $html = '<form class="ddys-discuz-request-form" method="post" action="' . ddys_open_attr($action) . '" data-ddys-discuz-request-form>';
    $html .= '<input type="hidden" name="formhash" value="' . ddys_open_attr(ddys_open_formhash()) . '" />';
    $html .= '<label>片名<input type="text" name="title" maxlength="255" required /></label>';
    $html .= '<label>年份<input type="number" name="year" min="1900" max="2099" /></label>';
    $html .= '<label>类型<select name="type"><option value=""></option><option value="movie">电影</option><option value="series">剧集</option><option value="variety">综艺</option><option value="anime">动漫</option></select></label>';
    $html .= '<label>豆瓣 ID<input type="text" name="douban_id" maxlength="30" /></label>';
    $html .= '<label>备注<textarea name="description" maxlength="1000"></textarea></label>';
    $html .= '<button type="submit">提交求片</button><p class="ddys-discuz-status" role="status"></p></form>';
    return ddys_open_wrap($html, $args);
}

function ddys_open_frontend_assets()
{
    static $printed = false;
    $settings = ddys_open_settings();
    if ($printed || empty($settings['enable_styles'])) {
        return '';
    }
    $printed = true;
    $base = ddys_open_plugin_url();
    return '<link rel="stylesheet" type="text/css" href="' . ddys_open_attr($base . 'static/css/frontend.css?v=' . DDYS_OPEN_VERSION) . '" />' .
        '<script defer src="' . ddys_open_attr($base . 'static/js/frontend.js?v=' . DDYS_OPEN_VERSION) . '"></script>';
}

function ddys_open_render_page($view, $params)
{
    $view = ddys_open_choice($view, array('latest', 'hot', 'search', 'calendar', 'movie', 'collections', 'requests'), 'latest');
    if ($view === 'hot') {
        return ddys_open_render_shortcode('ddys_hot', array('limit' => isset($params['limit']) ? $params['limit'] : 12));
    }
    if ($view === 'search') {
        return ddys_open_render_shortcode('ddys_search', array('q' => isset($params['q']) ? $params['q'] : '', 'type' => isset($params['type']) ? $params['type'] : 'movie'));
    }
    if ($view === 'calendar') {
        return ddys_open_render_shortcode('ddys_calendar', array('year' => isset($params['year']) ? $params['year'] : '', 'month' => isset($params['month']) ? $params['month'] : ''));
    }
    if ($view === 'movie') {
        return ddys_open_render_shortcode('ddys_movie', array('slug' => isset($params['slug']) ? $params['slug'] : ''));
    }
    if ($view === 'collections') {
        return ddys_open_render_shortcode('ddys_collections', array('page' => isset($params['page']) ? $params['page'] : 1));
    }
    if ($view === 'requests') {
        return ddys_open_render_shortcode('ddys_requests', array('page' => isset($params['page']) ? $params['page'] : 1));
    }
    return ddys_open_render_shortcode('ddys_latest', array('limit' => isset($params['limit']) ? $params['limit'] : 12));
}
