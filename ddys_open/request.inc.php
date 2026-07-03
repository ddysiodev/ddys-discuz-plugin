<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/ddys_open/source/bootstrap.inc.php';

if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    ddys_open_json_response(ddys_open_error('Method not allowed.', 405), 405);
}
if (!ddys_open_check_formhash()) {
    ddys_open_json_response(ddys_open_error('Formhash 校验失败。', 403), 403);
}
ddys_open_json_response(ddys_open_handle_request_form());

