<?php

include "../vendor/autoload.php";
use Firebase\JWT\JWT;

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
                "id"=>$id,
                "message"=>'logged in'
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
                        user_password=:user_password,
                        refer=:refer,
                        ref_code=:ref_code";
    
        $stmt = $this->conn->prepare($query);


        // sanitize
        $this->phone_number=htmlspecialchars(strip_tags($data->phone_number));
        $this->password=htmlspecialchars(strip_tags($data->password));

        // Verify the refer code
        $ref_id = $this->verifyRefer($data); 
        if ($ref_id){
            $this->refer = $ref_id;
            $stmt->bindValue(":refer", $this->refer);
        }else{
            $stmt->bindValue(":refer", "");
        }

        // bind data
        $stmt->bindValue(":phone_number", $this->phone_number);
        $stmt->bindValue(":user_password", password_hash($this->password, PASSWORD_BCRYPT));
        $stmt->bindValue(":ref_code", $this->generateReferCode());

        if ($this->userExist()) {
            return array("status"=>0,"message"=> "User already exist");
        }
    
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return array("success"=>["status"=> 1, "message"=>"Registration successfull"]);
        }
        return array("error"=>["status"=> 0, "message"=>"Error registering user"]);
                    
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

    public function verifyRefer($data){
        $query = "SELECT user_id FROM users WHERE ref_code=:refer_code";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":refer_code", $data->refer_code);
        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                return $user_id;
            }
        }
        return false;
    }

    public function generateReferCode(){
        $cstrong = true;
	    return strtoupper(date("dis")."".bin2hex(openssl_random_pseudo_bytes(2, $cstrong)));
    }


    public function getAll(): array
    {
        // /**
        //  * To get all the user from the database
        //  * @param - 
        //  * @return - associative array of order
        //  */
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = array();
        $data['total user'] = $stmt->rowCount();
        $data['users'] = array();
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

    public function get(string $id): array | false
    {
        $query = "SELECT * FROM ".$this->db_table." WHERE user_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
        $data['user'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $userArr = array(
                "user_id" => $user_id,
                "phone_number" => $phone_number,
                "ref_code"=>$ref_code,
            );
            array_push($data["body"], $userArr);
        }
        return $data;
    }

    public function delete(string $id): int
    {
        $query =    "DELETE FROM ".$this->db_table." WHERE user_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}