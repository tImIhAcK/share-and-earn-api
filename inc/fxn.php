<?php

// include "../vendor/autoload.php";
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;

// User Function
function userSwitch($url, $_db){
    switch ($url[3]):
        case "get_users":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user, $url[3]);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "get":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "login":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user, $url[3]);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        case "register":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user, $url[3]);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id, $url[3]);
            break;

        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    endswitch;
}

function orderSwitch($url, $_db){
    switch ($url[3]):
        case "get_orders":
            $id = $url[4] ?? null;
            $order = new Order($_db);
            $controller =  new OrderController($order);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get":
            $id = $url[4] ?? null;
            $order = new Order($_db);
            $controller =  new OrderController($order);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "create":
            $id = $url[4] ?? null;
            $order = new Order($_db);
            $controller =  new OrderController($order);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    endswitch;
}

function bankSwitch($url, $_db){
    switch ($url[3]) {
        case 'create':
            $id = $url[4] ?? null;
            $bank = new Bank($_db);
            $controller =  new BankController($bank);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get_banks":
            $id = $url[4] ?? null;
            $bank = new Bank($_db);
            $controller =  new BankController($bank);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "get":
            $id = $url[4] ?? null;
            $bank = new Bank($_db);
            $controller =  new BankController($bank);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;

        case "delete":
            $id = $url[4] ?? null;
            $bank = new Bank($_db);
            $controller =  new BankController($bank);
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