<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

define("BASE_DIR", "../../");
include_once BASE_DIR.'/config/bootstrap.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);
$data = json_decode(file_get_contents("php://input"));

$user->email = $data->email;
$stmt = $user->login();
$rowCount = $stmt->rowCount();

json_encode($rowCount);
if ($rowCount === 1) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        if (password_verify($data->password, $user_password)) {
            
            $user_details = array(
                "message" => "logged in",
                "id" => $id,
                "full_name" => $user_fullname,
                "phone_number" => $phone_number,
            );
        }else{
            http_response_code(400);
            echo json_encode(array("message" => "Invalid password"));
        }
        http_response_code(200);
        echo json_encode($user_details);
    }   
}else{
    http_response_code(400);
    echo json_encode(array("message" => "User does not exist"));
}