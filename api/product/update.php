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
    $item = new Product($db);
    $data = json_decode(file_get_contents("php://input"));
    
    $item->id = $data->id;
    
    // employee values
    $item->name = $data->name;
    $item->decription = $data->description;
    $item->image = $data->image;
    $item->price = $data->price;
    $item->catagory_id = $data->category_id;
    $item->status = $data->status;
    
    if($item->updateProduct()){
        echo json_encode("Product data updated.");
    } else{
        echo json_encode("Product could not be updated");
    }