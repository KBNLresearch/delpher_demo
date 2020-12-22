<?php
// Inloggen op de database
$dir = 'sqlite:/var/www/delpher_demo/db/test.db';
$dbh  = new PDO($dir) or die("cannot open the database");
?>
