<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once ('config.php');

if (!empty($_GET['type'])) {
    if (is_file(TEMPLATES.'/'.$_GET['type'].'.php')) {
        include (TEMPLATES.'/'.$_GET['type'].'.php');
    } else {
        define('STYLE', 'error');
        include ('templates/404.php');
    }
} else {
    define('STYLE', 'default');
    include ('templates/home.php');
}
