<?php

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/ddys_open/source/bootstrap.inc.php';

$op = ddys_open_get('op', 'home');
$baseUrl = ADMINSCRIPT . '?action=plugins&identifier=ddys_open&pmod=admincp';
$siteUrl = isset($_G['siteurl']) ? $_G['siteurl'] : '../';
$settings = ddys_open_settings();
$notice = '';

if ($op === 'flush' && ddys_open_get('formhash') === ddys_open_formhash()) {
    $count = ddys_open_cache_flush();
    $notice = '已清理缓存：' . intval($count) . ' 条。';
}
if ($op === 'test' && ddys_open_get('formhash') === ddys_open_formhash()) {
    $payload = ddys_open_api_get('/types', array(), array('no_cache' => true));
    $notice = ddys_open_is_error($payload) ? '连接测试失败：' . $payload['message'] : '连接测试成功。';
}

echo '<div class="ddys-discuz-admin">';
echo '<h2>低端影视</h2>';
if ($notice !== '') {
    echo '<div class="infotips">' . ddys_open_h($notice) . '</div>';
}
echo '<p>插件配置请在 Discuz 后台的插件变量里维护；这里提供诊断、缓存清理和短代码生成。</p>';
echo '<p><a class="btn" href="' . ddys_open_attr($baseUrl . '&op=test&formhash=' . ddys_open_formhash()) . '">测试低端影视 API</a> ';
echo '<a class="btn" href="' . ddys_open_attr($baseUrl . '&op=flush&formhash=' . ddys_open_formhash()) . '">清理缓存</a></p>';
echo '<table class="dt"><tr><th>项目</th><th>当前值</th></tr>';
echo '<tr><td>API Base URL</td><td>' . ddys_open_h($settings['api_base_url']) . '</td></tr>';
echo '<tr><td>缓存条数</td><td>' . intval(ddys_open_cache_count()) . '</td></tr>';
echo '<tr><td>求片表单</td><td>' . (!empty($settings['enable_request_form']) ? '已启用' : '未启用') . '</td></tr>';
echo '<tr><td>插件页面</td><td><a href="' . ddys_open_attr($siteUrl . 'plugin.php?id=ddys_open:index') . '" target="_blank">plugin.php?id=ddys_open:index</a></td></tr>';
echo '</table>';
echo '<h3>短代码生成器</h3>';
echo '<div class="ddys-discuz-generator">';
echo '<label>类型 <select id="ddys-discuz-shortcode-kind"><option value="ddys_latest">最新</option><option value="ddys_hot">热门</option><option value="ddys_search">搜索</option><option value="ddys_calendar">日历</option><option value="ddys_movie">影片详情</option><option value="ddys_sources">资源</option><option value="ddys_collections">片单</option><option value="ddys_request_form">求片表单</option></select></label>';
echo '<label>slug <input id="ddys-discuz-shortcode-slug" type="text" /></label>';
echo '<label>limit <input id="ddys-discuz-shortcode-limit" type="number" min="1" max="50" value="12" /></label>';
echo '<label>type <input id="ddys-discuz-shortcode-type" type="text" placeholder="movie" /></label>';
echo '<p><button type="button" class="btn" id="ddys-discuz-shortcode-build">生成</button></p>';
echo '<textarea id="ddys-discuz-shortcode-output" rows="6" style="width:100%" readonly>[ddys_latest limit="12"]</textarea>';
echo '<pre>[ddys_latest limit="12"]
[ddys_hot limit="10"]
[ddys_search]
[ddys_calendar year="2026" month="7"]
[ddys_movie slug="this-tempting-madness"]
[ddys_sources slug="this-tempting-madness"]
[ddys_request_form]</pre>';
echo '</div></div>';
echo '<link rel="stylesheet" type="text/css" href="../source/plugin/ddys_open/static/css/admin.css?v=' . DDYS_OPEN_VERSION . '" />';
echo '<script src="../source/plugin/ddys_open/static/js/admin.js?v=' . DDYS_OPEN_VERSION . '"></script>';
