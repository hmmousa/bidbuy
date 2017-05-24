<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
 
$ProductID = $_POST['ProductID'];
$UserID = $_POST['UserID'];

// get the conditions
echo $db->getProductByID($ProductID, $UserID);
?>