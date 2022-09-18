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
    $product = new Product($db);
    $product->id = isset($_GET['id']) ? $_GET['id'] : die();
  
    $product->getOneProduct();
    if($product->name != null){
        // create array
        $product_arr = array(
            "id" =>  $product->id,
            "name" => $product->name,
            "description" => $product->description,
            "image" => $product->image,
            "price" => $product->price,
            "status" => $product->status,
            "category_id" => $product->category_id
        );
      
        http_response_code(200);
        echo json_encode($product_arr);
    }
      
    else{
        http_response_code(404);
        echo json_encode("Product not found.");
    }
?>