<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_ddys_open_rate extends discuz_table
{
    public function __construct()
    {
        $this->_table = 'ddys_open_rate';
        $this->_pk = 'rate_key';
        parent::__construct();
    }
}

