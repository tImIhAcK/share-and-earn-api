<?php

require_once __DIR__ . "/vendor/autoload.php";
use CoinbaseCommerce\Webhook;

spl_autoload_register(function ($class) {
    require "../src/$class.php";
});

set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$database = new Database();
$_db = $database->connect();

$secret = 'SECRET_KEY';
$headerName = 'x-cc-webhook-wignature';
$headers = getallheaders();
$signraturHeader = isset($headers[$headerName]) ? $headers[$headerName] : null;
$payload = trim(file_get_contents('php://input'));
try {
    $event = Webhook::buildEvent($payload, $signraturHeader, $secret);

    if ($event->type === 'charge:pending'){

    }

    if ($event->type === 'charge:confirmed'){

    }

    if ($event->type === 'charge:failed'){

    }

    http_response_code(200);
    echo sprintf('Successully verified event with id %s and type %s.', $event->id, $event->type);
} catch (\Exception $exception) {
    http_response_code(400);
    echo 'Error occured. ' . $exception->getMessage();
}