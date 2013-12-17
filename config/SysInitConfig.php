<?php

//*******************************************************系统配置********************************************************
define('DAGGER_ALARM_LOG_API',          'http://alarm.dagger.com/log.php'); //监控大厅域名，监控大厅下载地址：
define('DAGGER_ALARM_TMPLOG_API',       'http://alarm.dagger.com/tmplog.php'); //监控大厅域名，监控大厅下载地址：
define('DAGGER_ALARM_DEBUG_API',        'http://alarm.dagger.com/debug.php');
define('DAGGER_ALARM_XHPROF_API',       'http://alarm.dagger.com/xhprof.php');
define('DAGGER_ALARM_XHPROF_SHOW_URL',  'http://alarm.dagger.com/tools/xhprof_html/index.php');

define('DAGGER_TEMPLATE_ENGINE', 'smarty');

define('DAGGER_APP_DATA', '/var/data/data/hope');
define('DAGGER_APP_CACHE', '/var/data/cache/hope');
define('DAGGER_APP_APPLOGS', '/var/data/applogs/hope');
define('PATH_CACHE', '/var/data/cache/mayhope/');

define( "WB_AKEY" , '1028039693' );
define( "WB_SKEY" , '94155f6b8e1e61275ab336b475a44ad7' );
define( "WB_CALLBACK_URL" , 'http://mayhope.com/?s=public&a=callback' );
define( "WB_BLOG_CALLBACK_URL" , 'http://mayhope.com/home/?s=public&a=callback' );

//******************************************************允许的POST REFERER***************************************************
$_SERVER['SERVER_ACCEPT_REFERER'] = array('hope.com', 'oicu.me', 'mayhope.com', 'lapbus.com');
