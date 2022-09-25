<?php

if (isset($_POST['reset-request-password'])) {
    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);

    $url = "http://localhost:8801/forgotpassword/ctreat-new-password.php?selector=" .$selector. "&validator=".bin2hex($token);
    $expires = date('U') + 1800;
    
    
    spl_autoload_register(function ($class) {
        require "../src/$class.php";
    });

    set_error_handler("ErrorHandler::handleError");
    set_exception_handler("ErrorHandler::handleException");

    $database = new Database();
    $_db = $database->connect();

    $userEmail = $_POST['email'];

    $query ="DELETE FROM pwdreset WHERE pwdResetEmail=:userEmail";
    $stmt = $_db->conn->prepare($query);


    $userEmail = htmlspecialchars(strip_tags($userEmail));


    $stmt->bindValue(":userEmail", $userEmail);
    $stmt->execute();

    $query =    "INSERT INTO 
                users
            SET
                phone_number=:phone_number,
                user_password=:user_password,
                user_email=:user_email,
                user_role=:user_role
                refer=:refer,
                ref_code=:ref_code";

    $stmt = $this->conn->prepare($query);


}else{
    http_response_code(404);
    echo json_encode(array("message"=>"Page not found"));
}


function validateEmail($userEmail, $_db): array
{
    $error = [];
    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $query =  "SELECT user_email FROM users WHERE user_email=:user_email";
        $stmt = $_db->conn->prepare($query);
        $userEmail = htmlspecialchars(strip_tags($userEmail));
        $stmt->bindValue(":userEmail", $userEmail);

        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            $error[] = "Invalid Email";
        }

    }else{
        $error[] = "Invalid Email";
    }
    return $error;
}