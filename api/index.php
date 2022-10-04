<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin *");
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
include "../vendor/autoload.php";
include "../inc/fxn.php";

$database = new Database();
$_db = $database->connect();


$url = explode("/", $_SERVER["REQUEST_URI"]);

switch($url[2]):
    case "user":
        userSwitch($url, $_db);
        break;
    case "order":
        orderSwitch($url, $_db);
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




//mysql://b89e1db581d092:7afa7e0f@us-cdbr-east-06.cleardb.net/heroku_1a4914ed9299b35?reconnect=true
// DB_USERNAME: b89e1db581d092
// DB_PASS: 7afa7e0f
// DB_HOST: us-cdbr-east-06.cleardb.net