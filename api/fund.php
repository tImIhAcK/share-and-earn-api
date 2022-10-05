<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
$amt = $_POST['amount'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.commerce.coinbase.com/charges/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$post = array(
    "name" => $trans_type,
    "description" => '',
    "local_price" => array(
        'amount' => $amt,
        'currency' => 'USD'
    ),
    "pricing_type" => "fixed_price",
    "metadata" => array(
        'customer_id' => $user_id,
    )
);

$post = json_encode($post);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "X-Cc-Api-Key: ebe7dbe0-664b-4445-9d45-4847098b5a4d";
$headers[] = "X-Cc-Version: 2018-03-22";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
curl_close ($ch);
$response = json_decode($result);
echo $response;
exit;

// Add to transaaction database
create($_db);
return $response->data->hosted_url;


function create($_db): array
{
    $query =    "INSERT INTO
                    transactions
                SET
                trans_type=:trans_type,
                trans_amt=:trans_amt,
                user_id=:user_id,
                trans_status=:trans_status";
    
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