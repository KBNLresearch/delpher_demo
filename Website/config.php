<?php

//echo '<pre>'.print_r($_SERVER, true).'</pre>';

define('ROOT', $_SERVER['DOCUMENT_ROOT']."/delpher_demo");
define('URL', 'http://delpher_demo.kbresearch.nl'); //$_SERVER['HTTP_HOST']);

define('TEMPLATES', ROOT.'/templates');

define('PUBLIC_URL', URL.'/public');
define('CSS', PUBLIC_URL.'/css');
define('IMG', PUBLIC_URL.'/img');

?>
