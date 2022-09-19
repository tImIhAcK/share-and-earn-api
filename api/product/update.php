<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    define("BASE_DIR", "../../");
    include_once BASE_DIR.'/config/bootstrap.php'; 
    
    $database = new Database();
    $db = $database->connect();
    $product = new Product($db);
    $data = json_decode(file_get_contents("php://input"));
    
    if (
        !empty($data->id)&&
        !empty($data->name)&&
        !empty($data->description)&&
        !empty($data->price)&&
        !empty($data->image)&&
        !empty($data->status)&&
        !empty($data->category_id)
    ) {
        $product->id = $data->id;
        $product->name = $data->name;
        $product->decription = $data->description;
        $product->image = $data->image;
        $product->price = $data->price;
        $product->catagory_id = $data->category_id;
        $product->status = $data->status;
        
        if($product->updateProduct()){
            http_response_code(200);
            echo json_encode(array('Message'=>"Product data updated."));
        } else{
            http_response_code(503);
            echo json_encode(array('Meaasge' => "Product could not be updated"));
        }
    }else{
        http_response_code(400);
        echo json_encode(array("Message"=>'Data is imcolplete'));
    }