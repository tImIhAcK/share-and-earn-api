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

echo json_encode($orderCount);
if($orderCount > 0){
    
    $orderArr = array();
    $orderArr["body"] = array();
    $orderArr["Total orders"] = $orderCount;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $order_item = array(
            "id"=>$id,
            "name"=>$fullname,
            "email"=>$email,
            "total"=>$total,
            "product_id" => $product_id,
            "product" => $product_name,
            'price' => $price,
            "onwer_id"=>$owner_id,
            "order_id"=>$order_id,
            "order_date"=>$created
        );
        array_push($orderArr["body"], $order_item);
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