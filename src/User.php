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

    public function login($data):array
    {
        $query =    "SELECT 
                        user_id, phone_number, user_password FROM users
                    WHERE 
                        phone_number=:phone_number";

        $stmt = $this->conn->prepare($query);

        $this->phone_number=htmlspecialchars(strip_tags($data->phone_number));
        $this->password=htmlspecialchars(strip_tags($data->password));
        // bind data
        $stmt->bindValue(":phone_number", $this->phone_number);

        if(!$this->verifyPassword()){
            return array("status"=>0, "message"=>"Invalid password");
        }
        
        if($stmt->execute()){
            return array("status"=>1, "message"=>"User logged in");
        }
        return array("status"=>0, "message"=>"Error occur");
    }

    public function verifyPassword(){
        $query = "SELECT user_password FROM users WHERE phone_number=:phone_number";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":phone_number", $this->phone_number);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(password_verify($this->password, $row['user_password'])){
                return true;
            }
        }
        return false;
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
        $stmt->bindValue(":phone_number", $this->phone_number);
        $stmt->bindValue(":user_password", password_hash($this->password, PASSWORD_BCRYPT));

        if ($this->userExist()) {
            return array("status"=>0,"message"=> "User already exist");
        }
    
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return array("status"=> 1, "message"=>"Registration successfull");
        }
        return array("status"=> 0, "message"=>"Error registering user");
                    
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
        // /**
        //  * To get all the user from the database
        //  * @param - 
        //  * @return - associative array of users
        //  */
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

    public function get(string $id): array
    {
        $query = "SELECT * FROM ".$this->db_table." WHERE user_id:=id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
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