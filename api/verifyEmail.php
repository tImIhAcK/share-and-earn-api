<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin *");
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
include "../vendor/autoload.php";
include "../inc/fxn.php";

$database = new Database();
$_db = $database->connect();


$data = json_decode(file_get_contents("php://input", true));

if(!empty($data->vselector) || !empty($data->vtoken)){
    if(ctype_xdigit($data->vselector) !== false && ctype_xdigit($data->vtoken) !== false){

        $query =    "SELECT
                        verified, vtoken, vselector
                    FROM
                        users
                    WHERE 
                        verified = 0
                    AND
                        vselector=:vselector
                    LIMIT
                        1";

            $stmt = $_db->conn->prepare($query);
            $vselector = htmlspecialchars(strip_tags($data->vselector));
            $stmt->bindValue(':vselector', $vselector);
            $stmt->execute();

            if ($stmt->rowCount() == 1 ) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $vTokenCheck = password_verify(hex2bin($data->vtoken), $vtoken);

                    if($vTokenCheck == true){

                        $query =    "UPDATE
                                        users
                                    SET
                                        verified=1
                                    WHERE
                                        vselector=:vselector
                                    LIMIT
                                        1";
                        $stmt = $_db->conn->prepare($query);
                        $stmt->bindValue(':vselector', $vselector);

                        if($stmt->execute()){
                            return array('status'=>1, 'message'=>"Your account has been verified");
                        }else{
                            return array('status'=>0, 'message'=>'Error... Contact developer for support');
                        }

                    }else{
                        return array('status'=>0, 'message'=>'Invalid verification token');
                    }
                }
            }else{
                return array('status'=>0, 'message'=>'Account has been verified');
            }

    }else{
        return array('status'=>0, "message"=>"Could not validate your request");
    }
}else{
    return array('status'=>0, "message"=>"Could not validate your request");
}



