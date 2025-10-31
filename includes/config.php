<?php
session_start();

define('SITE_NAME', 'BuyBit');
define('SITE_URL', 'http://localhost/buybit');

require_once 'database.php';
$database = new Database();
$db = $database->getConnection();

require_once 'functions.php';
?>