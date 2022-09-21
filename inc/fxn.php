<?php

// User Function
function userSwitch($url, $_db){
    switch ($url[3]):
        case "get_users":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
        case "login":
            $id = $url[4] ?? null;
            break;
        case "register":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
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
            $user = new User($_db);
            $controller =  new UserController($user);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
        case "get_user_orders":
            $id = $url[4] ?? null;
            break;
        case "create":
            $id = $url[4] ?? null;
            $user = new User($_db);
            $controller =  new UserController($user);
            $controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
            break;
        default:
            http_response_code(404);
            echo json_encode(array("message" => 'File not found'));
            break;
    endswitch;
}