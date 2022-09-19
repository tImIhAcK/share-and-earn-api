<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
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

const COOKIE_RUNTIME = 604800; // Cookie expire after 7 days if user do not log in
const COOKIE_RUNTIME_ = 259200; // Renew cookie every 3 days when user log in

json_encode($rowCount);
if ($rowCount > 0) {
    $result = array();
    $result["body"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if (password_verify($user->password, $row['password'])) {
            extract($row);
            $user_details = array(
                "user_id" => $user_id,
                "fullname" => $fullname,
                "email"=> $email,
                "phone number"=>$phone_number
            );
            $cstrong = True;
			$token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
            setcookie('SNID', $token, time() + COOKIE_RUNTIME, '/', NULL, NULL, TRUE);
			setcookie('SNID_', '1', time() + COOKIE_RUNTIME_, '/', NULL, NULL, TRUE);
            
            array_push($result["body"], $user_details);
        }else{
            echo json_encode(array("message" => "Invalid password"));
        }
        http_response_code(200);
        echo json_encode($result);
    }   
}else{
    echo json_encode(array("message" => "User does not exist"));
}