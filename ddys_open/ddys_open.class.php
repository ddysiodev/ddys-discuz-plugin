<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/ddys_open/source/bootstrap.inc.php';

class plugin_ddys_open
{
    public function global_header()
    {
        return ddys_open_frontend_assets();
    }

    public function global_nav_extra()
    {
        $settings = ddys_open_settings();
        if (empty($settings['show_nav'])) {
            return '';
        }
        return '<li><a href="' . ddys_open_attr(ddys_open_page_url('latest')) . '">低端影视</a></li>';
    }

    public function discuzcode($param = array())
    {
        global $_G;
        if (isset($_G['discuzcodemessage']) && strpos($_G['discuzcodemessage'], '[ddys_') !== false) {
            $_G['discuzcodemessage'] = ddys_open_parse_shortcodes($_G['discuzcodemessage']);
        }
    }

    public function global_footer()
    {
        return '';
    }
}

class plugin_ddys_open_forum extends plugin_ddys_open
{
    public function index_middle()
    {
        return ddys_open_hook_widget('index_widget_shortcode');
    }

    public function forumdisplay_bottom()
    {
        return ddys_open_hook_widget('forumdisplay_widget_shortcode');
    }

    public function viewthread_bottom()
    {
        return ddys_open_hook_widget('viewthread_widget_shortcode');
    }
}

class mobileplugin_ddys_open extends plugin_ddys_open
{
    public function global_header_mobile()
    {
        return ddys_open_frontend_assets();
    }
}
