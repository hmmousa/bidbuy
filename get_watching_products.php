<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
 
$UserID = $_POST['UserID'];

// get the conditions
echo $db->getWatchingProducts($UserID);
?>