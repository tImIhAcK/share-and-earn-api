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

$selector = bin2hex(random_bytes(8));
$token = random_bytes(32);

$url = "http://localhost:8801/create-new-password.php?selector=" .$selector. "&validator=".bin2hex($token);
$expires = date('U') + 1800;


$userEmail = $_POST['email'];

if(!validateEmail($userEmail, $_db)){
   return json_encode(array("status"=>0,"message"=> "Email does not exist"));
}

$query ="DELETE FROM pwdreset WHERE pwdResetEmail=:userEmail";
$stmt = $_db->conn->prepare($query);
$userEmail = htmlspecialchars(strip_tags($userEmail));
$stmt->bindValue(":userEmail", $userEmail);

if($stmt->execute()){
    $query =    "INSERT INTO 
                    pwdreset
                SET
                    pwdResetEmail=:pwdResetEmail,
                    pwdResetSelector=:pwdResetSeletor,
                    pwdResetToken=:pwdResetToken,
                    pwdResetExpires=:pwdResetExpires";

    $stmt = $this->conn->prepare($query);

    $stmt->bindValue(":pwdResetEmail", $userEmail);
    $stmt->bindValue(":pwdResetSelector", $selector);
    $stmt->bindValue(":pwdRestToken", password_hash($token, PASSWORD_BCRYPT));
    $stmt->bindValue(":pwdRestExpires", $expires);

    $stmt->execute();

}else{
    return json_encode(array("status"=>0, 'message'=>'Error occur'));
}



function validateEmail($userEmail, $_db): bool
{
    if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $query =  "SELECT user_email FROM users WHERE user_email=:userEmail";
        $stmt = $_db->conn->prepare($query);
        $userEmail = htmlspecialchars(strip_tags($userEmail));
        $stmt->bindValue(":userEmail", $userEmail);

        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            return true;
        }

        return false;
    }
}

function sendEmail($userEmail, $url){
    $to = $userEmail;
    $subject = 'Reset your password from Share and Earn';
    $message = "<p>
                    We recieved a password reset request. The link to reset your password is below.
                    If you do not make this request, you can ignore this email
                </p>";

    $message .= "<p>Here is your passwor reset link: <br>";
    $message .= '<a href="' .$url. '">' .$url. '</a></p>';

    $headers = "From: Share and Earn <adeniranjohn2016@gmail.com>\r\n";
    $headers .= "Reply-To: ";
    $headers .= "Content-type: text/html\r\n";

    mail($to, $subject, $message, $headers);

    return json_encode(array("status"=>1, "message"=>'Email sent successfully... check your mail for rest link'));

}