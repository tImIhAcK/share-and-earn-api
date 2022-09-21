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

$order->user_id = $data->user_id;
$stmt = $order->getUserOrder();
$orderCount = $stmt->rowCount();

if($orderCount > 0){
    // create array
    $orderArr = array();
    $orderArr["Total order"] = $orderCount;
    $orderArr["order"] = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $order_item = array(
            "order_no"=>$order_no,
            "order_type"=>$order_type,
            "order_quantity"=>$order_quantity,
            "order_price"=>$order_price,
            "order_owner"=>$order_owner,
            "order_date"=>$order_date
        );
        array_push($orderArr["order"], $order_item);
    }
    http_response_code(200);
    echo json_encode($orderArr);
}
    
else{
    http_response_code(404);
    echo json_encode(array("message"=>"No order"));
}
?>