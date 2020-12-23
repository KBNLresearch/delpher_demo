<?php
// Inloggen op de database
$dir = 'sqlite:/var/www/delpher_demo/db/newspapers_dummy.db';
$dbh  = new PDO($dir) or die("cannot open the database");
?>
