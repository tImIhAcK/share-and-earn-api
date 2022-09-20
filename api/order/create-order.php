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
$order = new Order($db);
$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->$fullname)&&
    !empty($data->email)&&
    !empty($data->address)&&
    !empty($data->total)&&
    !empty($data->product_id)&&
    !empty($data->product_name)&&
    !empty($data->price)&&
    !empty($data->owner_id)
) {

    $order->fullname = $data->name;
    $order->email = $data->email;
    $order->address = $data->address;
    $order->total = $data->total;
    $order->product_id = $data->product_id;
    $order->product_name = $data->product_name;
    $order->total = $data->total;
    $order->owner_id = $data->owner_id;
    
    if($product->addProduct()){
        http_response_code(201);
        echo json_encode(array('Message'=>'Order made successfully.'));
    } else{
        http_response_code(503);
        echo json_encode(array("Message"=>'Error making order'));
    }
}else{
    http_response_code(400);
    echo json_encode(array("Message"=>'Data is imcolplete'));
}
?>
