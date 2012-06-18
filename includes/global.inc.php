<?php
//global.inc.php

require_once 'classes/UserTools.class.php';
require_once 'classes/DB.class.php';

//connect to the database
$db = new DB();
$db->connect();

//initialize UserTools object
$userTools = new UserTools();

if (!isset($_SESSION)) {
    session_start();
}
?>
