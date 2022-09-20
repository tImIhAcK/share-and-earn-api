<?php

// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
define("BASE_DIR", "../../");
include_once BASE_DIR.'/config/bootstrap.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);
$stmt = $user->getUser();
$userCount = $stmt->rowCount();

// echo json_encode($itemCount);
if($userCount > 0){
    
    $user_arr = array();
    $user_arr["Total"] = $userCount;
    $user_arr["body"] = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $userArr = array(
            "user_id" => $user_id,
            "phone_number" => $phone_number
        );
        array_push($user_arr["body"], $userArr);
    }
    http_response_code(200);
    echo json_encode($user_arr);
}
else{
    http_response_code(404);
    echo json_encode(
        array("message" => "No product found.")
    );
}
?>