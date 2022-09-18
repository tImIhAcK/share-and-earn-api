<?php

// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
define("BASE_DIR", "../../");
require_once BASE_DIR.'/config/bootstrap.php';

$database = new Database();
$db = $database->connect();
$categories = new Category($db);
$stmt = $categories->getCategory();
$itemCount = $stmt->rowCount();

echo json_encode($itemCount);
if($itemCount > 0){
    
    $categoryArr = array();
    $categoryArr["body"] = array();
    $categoryArr["itemCount"] = $itemCount;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $category_item = array(
            "id" => $id,
            "name" => $name,
            'description' => html_entity_decode($description)
        );
        array_push($categoryArr["body"], $category_item);
    }
    http_response_code(200);
    echo json_encode($categoryArr);
}
else{
    http_response_code(404);
    echo json_encode(
        array("message" => "No categories found.")
    );
}
?>