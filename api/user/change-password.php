<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

define("BASE_DIR", "../../");
include_once BASE_DIR.'/config/bootstrap.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);
$data = json_decode(file_get_contents("php://input"));

if($data->new_password1 !== $data->new_password2){
    echo json_encode(array('message'=>'New password do not match'));
}
else{
    $user->$id = $data->$id;
    $user->old_password = $data->old_password;
    $user->new_password = $data->new_password2;


    if($user->changePassword()){
        http_response_code(200);
        echo json_encode(array('message'=>"Password change successfully"));
    } else{
        http_response_code(503);
        echo json_encode(array('message' => "Error changing password"));
    }
}