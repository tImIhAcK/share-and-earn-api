<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: localhost:8801");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Authorization: Basic ");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

spl_autoload_register(function ($class) {
    require "../src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");
// include "../vendor/autoload.php";
include "../inc/fxn.php";


$database = new Database();
$_db = $database->connect();
// use Firebase\JWT\JWT;


$url = explode("/", $_SERVER["REQUEST_URI"]);


switch($url[2]):
    case "user":
        userSwitch($url, $_db);
        break;
    case "order":
        orderSwitch($url, $_db);
        break;
    // case "bank":
    //     bankSwitch($url, $_db);
    //     break;
    case "transaction":
        transSwitch($url, $_db);
        break;
    case "product":
        productSwitch($url, $_db);
        break;
    case "admin":
        adminSwitch($url, $_db);
        break;
    default:
        http_response_code(404);
        echo json_encode(array("message"=>"File not fouond"));
        break;
endswitch;

