<?php
require('./dbconnect.php');
session_start();

$_SESSION = array();

session_destroy();

header('Location: join/top.php');
?>