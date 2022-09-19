<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

define("BASE_DIR", "../../");
include_once BASE_DIR.'/config/bootstrap.php';

session_start();

$database = new Database();
$db = $database->connect();
$user = new User($db);
$data = json_decode(file_get_contents("php://input"));

if(isset($_SESSION['auth'])){
    unset($_SESSION['user_id']);
    unset($_SESSION['auth']);
    unset($_SESSION['email']);
    session_destroy();

    echo json_encode(array('message'=> "logged out"));
}else{
    echo json_encode(array('message' => 'You are not logged in'));
}