<?php

class User 
{
    private $conn;
    // Table
    private $db_table = "users";
    // Columns
    public $id;
    public $phone_number;
    public $password;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function login(){
        $query = "SELECT user_id, phone_number, user_password FROM users
                    WHERE phone_number=:phone_number";
        $stmt = $this->conn->prepare($query);

        $this->phone_number=htmlspecialchars(strip_tags($this->phone_number));
        // bind data
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->execute();
        return $stmt;
    }

    public function register(){
        $query = "INSERT INTO users
                SET
                    phone_number=:phone_number,
                    user_password=:user_password";
    
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->phone_number=htmlspecialchars(strip_tags($this->phone_number));
        $this->password=htmlspecialchars(strip_tags($this->password));

    
        // bind data
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
        $query = "SELECT * FROM users WHERE phone_number=:phone_number";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":phone_number", $this->phone_number);
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
        $query = "SELECT * FROM users ORDER BY user_id ASC";
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