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

// if (
//     !empty($data->$order_type)&&
//     !empty($data->order_quantity)&&
//     !empty($data->order_price)&&
//     !empty($data->order_owner)
// ) {
    // $order->order_no = date("YmdHis")."".random_int(1000, 9999);
    $order->order_type = $data->order_type;
    $order->order_quantity = $data->order_quantity;
    $order->order_price = $data->order_price;
    $order->order_owner = $data->order_owner;

    if($order->createOrder()){
        http_response_code(201);
        echo json_encode(array('message'=>'Order made successfully.'));
    } else{
        http_response_code(503);
        echo json_encode(array("message"=>'Error making order'));
    }
// }else{
//     http_response_code(400);
//     echo json_encode(array("message"=>'Data is imcolplete'));
// }
?>
