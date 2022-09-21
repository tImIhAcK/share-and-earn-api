<?php

declare(strict_types=1);

spl_autoload_register(function ($class) {
    require "../src/$class.php";
});

// set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$_db = $database->connect();
$url = explode("/", $_SERVER["REQUEST_URI"]);

include "../inc/fxn.php";

switch($url[2]):
    case "user":
        userSwitch($url, $_db);
        break;

    case "order":
        orderSwitch($url, $_db);
        break;
    default:
        http_response_code(404);
        echo json_encode(array("message"=>"File not fouond"));
        break;
endswitch;

