<?php

// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
define("BASE_DIR", "../../");
require_once BASE_DIR.'/config/bootstrap.php';

$database = new Database();
$db = $database->connect();
$orders = new Order($db);
$stmt = $orders->getOrders();
$orderCount = $stmt->rowCount();

if($orderCount > 0){    
    $orderArr = array();
    $orderArr["Total orders"] = $orderCount;
    $orderArr["order"] = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $order_item = array(
            "order_no"=>$order_no,
            "order_type"=>$order_type,
            "order_quantity"=>$order_quantity,
            "order_price"=>$order_price,
            "order_owner"=>$order_owner
        );
        array_push($orderArr["order"], $order_item);
    }
    http_response_code(200);
    echo json_encode($orderArr);
}
else{
    http_response_code(404);
    echo json_encode(
        array("message" => "No order found.")
    );
}
?>