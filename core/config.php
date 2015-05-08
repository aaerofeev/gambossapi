<?php

define ('APPLICATION_DEBUG', TRUE);
define ('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/..') . DIRECTORY_SEPARATOR);
define ('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
define ('BASE_URL', '/');

set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_ROOT);

if (APPLICATION_DEBUG) {

    error_reporting(E_ALL);
    ini_set('display_errors', true);
    ini_set('display_startup_errors', true);
}

$mysqlHost = '127.0.0.1';

date_default_timezone_set('Asia/Almaty');

define ('GAMEBOSS_API', 74393);
define ('ALAWAR_API', 40626);
define ('DATABASE_DSN', "mysql:host={$mysqlHost};dbname=jezzy;charset=utf8");
define ('DATABASE_USER', '');
define ('DATABASE_PASSWORD', '');

// Requires
require_once 'core/cache.php';
require_once 'core/image.php';
require_once 'core/database.php';
require_once 'core/core.php';

// Cache
$cache = Cache::setup(APPLICATION_ROOT . 'cache');

// Image
Image::setup(APPLICATION_ROOT . 'games');
