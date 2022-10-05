<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: localhost:8801");
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

$selector = $_POST['selector'];
$token = $_POST['token'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if (!empty($selector) || !empty($token)){
    if(ctype_xdigit($selector) !== false && ctype_xdigit($token) !== false){
        if (!empty($password) || !empty($confirm_password)){
            if($password == $confirm_password){

                
                $currentTime = date('U');
                $sql =  "SELECT * 
                        FROM 
                            pwdreset
                        WHERE
                            pwdResetSelector=:pwdResetSelector,
                        AND
                            pwdResetExpires>=:curentTime";
                $stmt = $_db->conn->prepare($sql);

                $stmt->bindValue(":pwdResetSelector", $pwdRestSelector);
                $stmt->bindValue(":pwdResetExpires", $currentTime);
                $stmt->execute();

                if ($stmt->rowCount() == 1 ) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);
                        $tokenBin = hex2bin($token);
                        $tokenCheck = password_verify($tokenBin, $pwdResetToken);

                        if ($tokenCheck === true) {
                            $tokenEmail = $pwdResetEmail;

                            if(validateEmail($_db, $tokenEmail)){
                                $sql =  "UPDATE
                                            users
                                        SET
                                            user_password=:user_password
                                        WHERE
                                            user_email=:user_email";
                                $stmt = $_db->conn->prepare($sql);
    
                                // Update user password
                                $stmt->bindValue(":user_email", $tokenEmail);
                                $stmt->bindVale(":user_password", password_hash($password, PASSWORD_BCRYPT));
                                $stmt->execute();

                                $query ="DELETE FROM pwdreset WHERE pwdResetEmail=tokenEmail";
                                $stmt = $_db->conn->prepare($query);
                                $stmt->bindValue(":tokenEmail", $tokenEmail);
                                $stmt->execute();

                                return json_encode(array("status"=>1, "message"=>"Password changed successfully"));

                            }else{
                                 return json_encode(array("status"=>0, "message"=>"There was an error"));
                            }

                            
                        } elseif($tokenCheck === false) {
                            return json_encode(array("status"=>0, "message"=>"You need to re-submit your reset request"));
                        }
                        
                    }
                }else{
                    return json_encode(array("status"=>0, "message"=>"You need to re-submit your reset request"));
                }
                

            }else{
                return json_encode(array("status"=>0, "message"=>"Password not matching"));
            }
        }else{
            return json_encode(array("status"=>0, "message"=>"All fields are required"));
        }
    }else{
        return json_encode(array("status"=>0, "message"=>"Could not validate your request"));
    }
}else{
    return json_encode(array("status"=>0, "message"=>"Could not validate your request"));
}


function validateEmail($_db, $tokenEmail)
{
    $query = "SELECT * FROM users WHERE user_email=:user_email";
    $stmt = $_db->conn->prepare($query);

    $stmt->bindParam(":user_email", $tokenEmail);
    $stmt->execute();

    if ($stmt->rowCount() == 1 ) {
        return true;
    }
    return false;
}