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
        $query =    "SELECT 
                        user_id, phone_number, user_password FROM users
                    WHERE 
                        phone_number=:phone_number";

        $stmt = $this->conn->prepare($query);

        $this->phone_number=htmlspecialchars(strip_tags($this->phone_number));
        // bind data
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->execute();
        return $stmt;
    }

    public function register($data): array
    {
        $query =    "INSERT INTO 
                        users
                    SET
                        phone_number=:phone_number,
                        user_password=:user_password";
    
        $stmt = $this->conn->prepare($query);


        // sanitize
        $this->phone_number=htmlspecialchars(strip_tags($data->phone_number));
        $this->password=htmlspecialchars(strip_tags($data->password));

        // bind data
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":user_password", password_hash($this->password, PASSWORD_BCRYPT));

        if ($this->userExist()) {
            return array("message"=> "User already exist");
        }
    
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return array("message"=> true);
        }
        return array("message"=> false);
                    
    }


    public function userExist(): bool
    {
        $query = "SELECT * FROM users WHERE phone_number=:phone_number";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            return true;
        }
        return false;
    }


    public function getAll(): array
    {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = array();
        $data['total user'] = $stmt->rowCount();
        $data['body'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $userArr = array(
                "user_id" => $user_id,
                "phone_number" => $phone_number
            );
            array_push($data["body"], $userArr);
        }
        return $data;

    }

}