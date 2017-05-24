<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
 
$Search = $_POST['Search'];

// get the conditions
echo $db->getProductsWithoutImages($Search);
?>