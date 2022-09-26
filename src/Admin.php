<?php

include "../vendor/autoload.php";
use Firebase\JWT\JWT;

class Admin 
{
    private $conn;
    // Table
    private $db_table = "users";
    // Columns
    public $id;
    public $phone_number;
    public $password;
    public $role;
    public $isAdmin;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function login($data):array
    {
        $query =    "SELECT 
                        user_id, phone_number, user_password 
                    FROM
                        " .$this->db_table."
                    WHERE 
                        phone_number=:phone_number";

        $stmt = $this->conn->prepare($query);

        $this->phone_number=htmlspecialchars(strip_tags($data->phone_number));
        $this->password=htmlspecialchars(strip_tags($data->password));
        // bind data
        $stmt->bindValue(":phone_number", $this->phone_number);

        if(!$this->verifyPassword()){
            return array("error"=>["status"=>0, "message"=>"Invalid password"]);
        }
        
        if($stmt->execute()){
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                $id = $user_id; 
                // $payload = array(
                //     'iss'=> 'localhost',
                //     'iat'=> time(),
                //     'exp'=> time() + 20000,
                //     'data' => [
                //         'id'=> $user_id
                //     ]
                // );
            }
            return ["success"=>[
                "status"=>1,
                "user_id"=>$id,
                "message"=>'logged in successful'
            ]];

            // $secret_key = "earn_and_share";
            // $jwt = JWT::encode($payload, $secret_key, 'HS256');
            // return [
            //     "success"=>[
            //         "status"=>1,
            //         "token"=>$jwt,
            //         "message"=>"logged in successfull"
            //         ]
            //     ];
        }
        return array("status"=>0, "message"=>"Error occur");
    }

    public function verifyPassword(){
        $query =    "SELECT 
                        user_password
                    FROM 
                        ".$this->db_table." 
                    WHERE 
                        phone_number=:phone_number";
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
                        ".$this->db_table."
                    SET
                        phone_number=:phone_number,
                        user_password=:user_password,
                        user_email=:user_email,
                        role=:role,
                        isAdmin=:isAdmin,
                        ref_code=:ref_code";
    
        $stmt = $this->conn->prepare($query);


        // sanitize
        $this->phone_number=htmlspecialchars(strip_tags($data->phone_number));
        $this->email=htmlspecialchars(strip_tags($data->email));
        $this->password=htmlspecialchars(strip_tags($data->password));
        $this->user_email=htmlspecialchars(strip_tags($data->email));
        $this->role=htmlspecialchars(strip_tags($data->role));

        // bind data
        $stmt->bindValue(":phone_number", $this->phone_number);
        $stmt->bindValue(":user_password", password_hash($this->password, PASSWORD_BCRYPT));
        $stmt->bindValue(":user_email", $this->email);
        $stmt->bindValue(":role", $this->role);
        $stmt->bindValue(":isAdmin", 1, PDO::PARAM_BOOL);
        $stmt->bindValue(":ref_code", $this->generateReferCode(), PDO::PARAM_STR);

        if ($this->phoneExist()) {
            return array("status"=>0,"message"=> "Phone number already exist");
        }

        if ($this->emailExist()) {
            return array("status"=>0,"message"=> "Email already exist");
        }
    
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return array("success"=>["status"=> 1, "message"=>"Registration successfull"]);
        }
        return array("error"=>["status"=> 0, "message"=>"Error registering user"]);
                    
    }


    public function phoneExist(): bool
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

    public function emailExist(): bool
    {
        $query = "SELECT * FROM users WHERE user_email=:user_email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_email", $this->user_email);
        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            return true;
        }
        return false;
    }

    public function generateReferCode(){
        $cstrong = true;
	    return strtoupper(date("dis")."".bin2hex(openssl_random_pseudo_bytes(2, $cstrong)));
    }


}