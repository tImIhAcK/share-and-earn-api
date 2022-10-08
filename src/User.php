<?php

// include "../vendor/autoload.php";
// use Firebase\JWT\JWT;

class User 
{
    private $conn;
    // Table
    private $db_table = "users";
    // Columns
    public $id;
    public $full_name;
    public $password;
    public $user_email;
    public $refer;
    public $vtoken;
    public $vselector;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function login($data):array
    {
        $query =    "SELECT 
                        w.address as wallet_address, w.balance  as balance, user_id,
                        full_name, user_email, user_password, verified
                    FROM 
                        users
                    LEFT JOIN
                        wallet w
                    ON
                        user.user_id = w.user_id
                    WHERE 
                        user_email=:user_email";

        $stmt = $this->conn->prepare($query);

        $this->user_email=htmlspecialchars(strip_tags($data->email));
        $this->password=htmlspecialchars(strip_tags($data->password));
        // bind data
        $stmt->bindValue(":user_email", $this->user_email);

        if(!$this->verifyPassword()){
            return array("error"=>["status"=>0, "message"=>"Invalid password"]);
        }
        
        if($stmt->execute()){
            $data = array();
            $data['status'] = '1';
            $data['user'] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                if($verified == 1){
                    $userArr = [
                        'user_id'=> $user_id,
                        'full_name'=>$full_name,
                        'email'=>$user_email,
                        'verified'=>$verified,
                        'people_refered'=>$this->getReferPeople($user_id),
                        'wallet_address'=>$wallet_address,
                        'balance'=>$balance
                    ];
                    array_push($data['user'], $userArr);
                }else{
                    return array('status'=>0, 'message'=>'Account is not verified. check your mail');
                }
            }
            return $data;

                // $payload = array(
                //     'iss'=> 'localhost',
                //     'iat'=> time(),
                //     'exp'=> time() + 20000,
                //     'data' => [
                //         'id'=> $user_id
                //     ]
                // );
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
        $query = "SELECT user_password FROM users WHERE user_email=:user_email";
        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":user_email", $this->user_email);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(password_verify($this->password, $row['user_password'])){
                return true;
            }
        }
        return false;
    }

    public function getReferPeople($user_id): array{
        $query =    "SELECT full_name FROM users WHERE refer=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $user_id);
        $stmt->execute();
        
        if($stmt->rowCount()>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                return $row;
            }
        }
        return array();
    }

    public function register($data): array
    {
        $query =    "INSERT INTO 
                        ".$this->db_table."
                    SET
                        full_name=:full_name,
                        user_password=:user_password,
                        user_email=:user_email,
                        vselector=:vselector
                        vtoken=:vtoken,
                        is_referred=:is_referred,
                        referral_code=:ref_code";
    
        $stmt = $this->conn->prepare($query);

        $this->vselector = bin2hex(random_bytes(8));
        $this->vtoken = random_bytes(32);


        // sanitize
        $this->full_name=htmlspecialchars(strip_tags($data->full_name));
        $this->password=htmlspecialchars(strip_tags($data->password));
        $this->user_email=htmlspecialchars(strip_tags($data->email));

        // Verify the refer code
        $ref_result = $this->verifyRefer($data);
        echo $ref_result;
        exit;
        if ($ref_result){
            $stmt->bindValue(":is_referred", (int)1, PDO::PARAM_INT);
        }

        // bind data
        $stmt->bindValue(":full_name", $this->full_name);
        $stmt->bindValue(":user_email", $this->user_email);
        $stmt->bindValue(":user_password", password_hash($this->password, PASSWORD_BCRYPT));
        $stmt->bindValue(':vselector', $this->vselector);
        $stmt->bindValue("vtoken", password_hash($this->vtoken, PASSWORD_BCRYPT));
        $stmt->bindValue(":ref_code", $this->generateReferCode());

        // // if ($this->phoneExist()) {
        //     return array("status"=>0,"message"=> "Phone number already exist");
        // }

        if ($this->emailExist()) {
            return array("status"=>0,"message"=> "Email already exist");
        }
    
        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            $this->createWallet();


            $url = "https://earn-and-share.000webhostapp.com/api/verifyEmail.php?vselector=" .$this->vselector. "&vtoken=".bin2hex($this->vtoken);
            $to = $this->user_email;
            $subject = 'Verify Email';
            $message = "<p>
                            Click the link below to verify your account
                        </p>";

            $message .= "<p>Here is the link: <br>";
            $message .= '<a href="' .$url. '">' .$url. '</a></p>';

            $headers = "From: Share and Earn <adeniranjohn2016@gmail.com>\r\n";
            $headers .= "Reply-To: ";
            $headers .= "Content-type: text/html\r\n";

            // mail($to, $subject, $message, $headers);

            return array("status"=> 1, "message"=>"Registration successful. Check your mail for the verification link");
        }
        return array("error"=>["status"=> 0, "message"=>"Error registering user"]);
                    
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

    public function verifyRefer($data)
    {
        $query = "SELECT user_id, full_name FROM users WHERE referral_code=:referral_code LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":referral_code", $data->referral_code);
        $stmt->execute();

        if ($stmt->rowCount() == 1 ) {
            $result = array();
            $result['body'] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $referArr = array(
                    'referral_id'=>$user_id,
                    'referral_name'=>$full_name
                );
            }
            array_push($result['body'], $referArr);
        }
        return false;
    }

    public function generateReferCode(){
        $cstrong = true;
	    return strtoupper(date("dis")."".bin2hex(openssl_random_pseudo_bytes(2, $cstrong)));
    }

    public function createWallet(){
        $query =    "INSERT INTO
                        wallet
                    SET
                        user_id=:id
                        wallet_address=:wallet_address,
                        balance=:balance
                    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $this->id);
        $stmt->execute();
        
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
        $data['user'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $userArr = array(
                "user_id" => $user_id,
                "full_name" => $full_name,
                "user_email"=>$user_email,
                "refer_code"=>$ref_code,
                "refer_count"=>$refer_count,
                "balance"=>$balance
            );
            array_push($data["user"], $userArr);
        }
        return $data;
    }

    public function get(string $id): array
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
                "full_name" => $full_name,
                "user_email"=>$user_email,
                "ref_code"=>$ref_code,
            );
            array_push($data["user"], $userArr);
        }

        if(empty($data)){
            return array("status"=>0, 'message'=>'No user with the id '.$id);
        }
        return $data;
    }

    public function delete(string $id): int
    {
        $query ="DELETE FROM ".$this->db_table." WHERE user_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}