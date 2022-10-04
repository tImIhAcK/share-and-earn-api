<?php

// include "../vendor/autoload.php";
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;


// Admin switch function
function adminSwitch($url, $_db){

    $id = $url[4] ?? null;
    $admin = new Admin($_db);
    $controller =  new AdminController($admin, $url[3]);

    switch ($url[3]):
        case "login":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "register":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    endswitch;
}

// User Controller  Function
function userSwitch($url, $_db){

    $id = $url[4] ?? null;
    $user = new User($_db);
    $controller =  new UserController($user, $url[3]);

    switch ($url[3]):
        case "get_users":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "get":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "login":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "register":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "delete":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    endswitch;
}


// Order Controller Function
function orderSwitch($url, $_db){

    $id = $url[4] ?? null;
    $order = new Order($_db);
    $controller =  new OrderController($order);

    switch ($url[3]):
        case "get_orders":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "create":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "delete":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    endswitch;
}

// Transaction Controller Function
function transSwitch($url, $_db){

    $id = $url[4] ?? null;
    $trans = new Transaction($_db);
    $controller =  new TransactionController($trans);

    switch ($url[3]) {
        case 'charge':
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get_trans":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "update":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "delete":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    }
}

// Product Controller Function
function productSwitch($url, $_db){

    $id = $url[4] ?? null;
    $prod = new Product($_db);
    $controller =  new ProductController($prod);

    switch ($url[3]) {
        case 'create':
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get_banks":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "update":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "delete":
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    }
}

// function decodeToken(){
//     header("Authorization: ");
//     $allheaders = getallheaders();
//     $jwt = $allheaders['Authorization'];

//     $secret_key = "earn_and_share";
//     $user_data = JWT::decode($jwt, new Key($secret_key, "HS256"));
//     $id = $user_data->data->id;
// }