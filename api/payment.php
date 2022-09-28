<?php

$user_id = $_POST['user_id'];
$prod_name = $_POST['prod_name'];
$prod_desc = $_POST['prod_desc'];
$prod_price = $_POST['prod_price'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.commerce.coinbase.com/charges/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$post = array(
    "name" => $prod_name,
    "description" => $prod_desc,
    "local_price" => array(
        'amount' => $prod_price,
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
echo $response->data->hosted_url;
