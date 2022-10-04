<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: localhost:8801");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Authorization: Basic ");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

spl_autoload_register(function ($class) {
    require "../src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$database = new Database();
$_db = $database->connect();

$user_id = $_POST['user_id'];
$trans_type = $_POST['trans_type'];
$amt = $_POST['amount'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.commerce.coinbase.com/charges/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);