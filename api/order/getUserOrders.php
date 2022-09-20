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
$orders = new Order($db);
$product->id = isset($_GET['id']) ? $_GET['id'] : die();

$product->getOneProduct();
if($product->name != null){
    // create array
    $orderArr = array();
    $orderArr["body"] = array();
    $orderArr["Total user order"] = $orderCount;
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
            "onwer_id"=>$ownerid,
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
    echo json_encode("Product not found.");
}
?>