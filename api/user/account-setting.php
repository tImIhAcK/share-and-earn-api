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

$user->id = $data->id;
$user->fullname = $data->fullname;
$user->phone_number = $data->phone_number;


if($user->updateAccount()){
    http_response_code(200);
    echo json_encode(array('message'=>"Account updated."));
} else{
    http_response_code(503);
    echo json_encode(array('message' => "Error updating account"));
}