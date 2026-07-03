<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_ddys_open_cache extends discuz_table
{
    public function __construct()
    {
        $this->_table = 'ddys_open_cache';
        $this->_pk = 'cache_key';
        parent::__construct();
    }
}

