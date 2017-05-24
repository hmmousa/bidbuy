<?php
 
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['name']) && isset($_POST['desc']) && isset($_POST['price']) && isset($_POST['category']) && isset($_POST['zipCode']) && isset($_POST['uid']) && isset($_POST['state'])) {
 
    // receiving the post params
	$latitude = null;
	$longitude = null;
	$image1 = null;
	$image2 = null;
	$image3 = null;
	$image4 = null;
	$image5 = null;
	
    $name = $_POST['name'];
    $desc = $_POST['desc'];
    $price = $_POST['price'];
	$category = $_POST['category'];
	$state = $_POST['state'];
	$zipCode = $_POST['zipCode'];
	$uid = $_POST['uid'];
	if(isset($_POST['latitude'])){
		$latitude = $_POST['latitude'];
	}
	if(isset($_POST['longitude'])){
		$longitude = $_POST['longitude'];
	}
	if(isset($_POST['image1'])){
		$image1 = $_POST['image1'];
	}
	if(isset($_POST['image2'])){
		$image2 = $_POST['image2'];
	}
	if(isset($_POST['image3'])){
		$image3 = $_POST['image3'];
	}
	if(isset($_POST['image4'])){
		$image4 = $_POST['image4'];
	}
	if(isset($_POST['image5'])){
		$image5 = $_POST['image5'];
	}
   
	// create a new 
	$product = $db->addProduct($name, $desc, $price, $category, $state, $zipCode, $latitude, $longitude, $image1, $image2, $image3, $image4, $image5, $uid);
	if ($product) {
		// product stored successfully
		$response["error"] = FALSE;
		echo json_encode($response);
	} else {
		// user failed to store
		$response["error"] = TRUE;
		$response["error_msg"] = "Unknown error occurred in adding!";
		echo json_encode($response);
	}
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameter is missing!";
    echo json_encode($response);
}
?>