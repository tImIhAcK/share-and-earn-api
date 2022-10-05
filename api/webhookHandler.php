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


function update($_db): array
{
    $query =    "UPDATE
                    transactions
                SET
                    trans_status=:trans_status
                WHERE
                    ";
    
    $stmt = $_db->conn->prepare($query);

    // sanitize
    $trans_type=htmlspecialchars(strip_tags($_POST['trans_type']));
    $trans_amt=htmlspecialchars(strip_tags($_POST['trans_amt']));
    $user_id=htmlspecialchars(strip_tags($_POST['user_id']));


    // bind data
    $stmt->bindValue(":trans_type", $trans_type, PDO::PARAM_STR);
    $stmt->bindValue(":trans_amt", $trans_amt, PDO::PARAM_INT);
    $stmt->bindValue(":trans_status", 'Initialized', PDO::PARAM_STR);
    $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

    if($stmt->execute()){
        $data = array();
        $data['transaction'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $transArr = [
                'trans_type'=> $trans_type,
                'trans_amt'=>$trans_amt,
                'trans_status'=>$trans_status
            ];
            array_push($data['transaction'], $transArr);
        }
        return $data;
    }
    return array(
                "status"=>false,
                'message'=>'Something went wrong... please try again',
            );
}