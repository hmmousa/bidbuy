<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['UserID']) && isset($_POST['ProductID'])) {
 
    // receiving the post params
    $UserID = $_POST['UserID'];
	$ProductID = $_POST['ProductID'];
 
    // add rate
    $result = $db->addWatching($UserID, $ProductID);
 
    if ($result != false) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Add watching list fail !";
        echo json_encode($response);
    }
} else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters userID or bank name is missing!";
    echo json_encode($response);
}
?>