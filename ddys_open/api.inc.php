<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/ddys_open/source/bootstrap.inc.php';

ddys_open_json_response(ddys_open_proxy_response());

