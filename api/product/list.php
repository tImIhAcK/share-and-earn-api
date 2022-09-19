<?php

// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
define("BASE_DIR", "../../");
include_once BASE_DIR.'/config/bootstrap.php';

$database = new Database();
$db = $database->connect();
$product = new Product($db);
$stmt = $product->getProduct();
$itemCount = $stmt->rowCount();

// echo json_encode($itemCount);
if($itemCount > 0){
    
    $productsArr = array();
    $productsArr["body"] = array();
    $productsArr["itemCount"] = $itemCount;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $product_items = array(
            "id" => $id,
            "name" => $name,
            'description' => html_entity_decode($description),
            "price" => '$'.$price,
            "image" => $image,
            'status' => $status,
            "category_id" => $category_id,
            "category_name" => $category_name,
        );
        array_push($productsArr["body"], $product_items);
    }
    http_response_code(200);
    echo json_encode($productsArr);
}
else{
    http_response_code(404);
    echo json_encode(
        array("message" => "No product found.")
    );
}
?>