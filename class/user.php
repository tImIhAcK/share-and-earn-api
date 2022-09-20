<?php

class User 
{
    private $conn;
    // Table
    private $db_table = "users";
    // Columns
    public $id;
    public $fullname;
    public $email;
    public $phone_number;
    public $password;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function login(){
        $query = "SELECT id, user_fullname, user_email, phone_number, user_password FROM users
                    WHERE user_email=:user_email";
        $stmt = $this->conn->prepare($query);

        $this->email=htmlspecialchars(strip_tags($this->email));
        // bind data
        $stmt->bindParam(":user_email", $this->email);
        $stmt->execute();
        return $stmt;
    }

    public function register(){
        $query = "INSERT INTO users
                SET
                    user_fullname = :user_fullname,
                    user_email = :user_email,
                    phone_number=:phone_number,
                    user_password = :user_password,
                    referral_code=:referral_code";
    
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->fullname=htmlspecialchars(strip_tags($this->fullname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->phone_number=htmlspecialchars(strip_tags($this->phone_number));
        $this->password=htmlspecialchars(strip_tags($this->password));
    
        // bind data
        $stmt->bindParam(":user_fullname", $this->fullname);
        $stmt->bindParam(":user_email", $this->email);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":user_password", password_hash($this->password, PASSWORD_BCRYPT));

        if ($this->userExist()) {
            echo json_encode(array("message"=> "User already exist"));
        }
    
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function userExist(){
        $query = "SELECT * FROM users WHERE user_email=:user_email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_email", $this->email);
        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            return true;
        }
        return false;
    }

    public function validateReferralCode(){
        $query = "SELECT * FROM users WHERE referral_code=:referral_code";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":referral_code", $this->referral_code);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            return true;
        }
        return false;
    }
    
    public function getUser(){
        $query = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateAccount(){
        $query = "UPDATE users
                SET
                    user_fullname=:user_fullname, 
                    phone_number=:phone_number
                WHERE 
                    id=:id";
    
        $stmt = $this->conn->prepare($query);
    
        $this->user_fullname=htmlspecialchars(strip_tags($this->fullname));
        $this->phone_number=htmlspecialchars(strip_tags($this->phone_number));
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        // bind data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_fullname", $this->fullname);
        $stmt->bindParam(":phone_number", $this->phone_number);
    
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function changePassword(){
        $query = "SELECT user_password FROM users WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        if($stmt->rowCount() === 1){
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                if (password_verify($this->old_password, $user_password)) {
                    $query = "UPDATE users
                        SET
                            user_password=:user_password
                        WHERE 
                            id=:id";
                    $stmt = $this->conn->prepare($query);

                    $this->new_password=htmlspecialchars(strip_tags($this->new_password));
                    $this->id=htmlspecialchars(strip_tags($this->id));

                    $stmt->bindParam(":id", $this->id);
                    $stmt->bindParam(":user_password", password_hash($this->new_password, PASSWORD_BCRYPT));

                    if($stmt->execute()){
                        return true;
                    }
                    return false;
                }else{
                    echo json_encode(array('message'=> 'Incorrect old password'));
                }
            }
            
        }else{
            return false;
        }

    }

}