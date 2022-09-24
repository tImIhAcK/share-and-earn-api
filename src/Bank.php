<?php

include "../vendor/autoload.php";
use Firebase\JWT\JWT;

class Bank 
{
    private $conn;
    // Table
    private $db_table = "banks";
    // Columns
    public $id;
    public $back_name;
    public $acct_name;
    public $acct_no;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM ".$this->db_table."";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = array();
        $data['bank'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract($row);
            $data['bank'] = $row;
        }
        return $data;
    }

    public function get(string $id): array | false
    {
        $query = "SELECT * FROM ".$this->db_table." WHERE bank_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
        $data['bank'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $data['bank'] = $row;

        }
        return $data;
    }

    public function create($data): array
    {
        $query =    "INSERT INTO
                    ". $this->db_table ."
                    SET
                    acct_name=:acct_name,
                    bank_name=:bank_name,
                    acct_no=:acct_no,
                    bank_id=:bank_id";
        
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->acct_name=htmlspecialchars(strip_tags($data->acct_name));
        $this->bank_name=htmlspecialchars(strip_tags($data->bank_name));
        $this->acct_no=htmlspecialchars(strip_tags($data->acct_no));
        $this->bank_id=htmlspecialchars(strip_tags($data->id));

    
        // bind data
        $stmt->bindValue(":acct_name", $this->acct_name, PDO::PARAM_STR);
        $stmt->bindValue(":bank_name", $this->bank_name, PDO::PARAM_STR);
        $stmt->bindValue(":acct_no", $this->acct_no, PDO::PARAM_INT);
        $stmt->bindValue(":bank_id", (int)$this->bank_id, PDO::PARAM_INT);
    
        if($stmt->execute()){
            $data = array();
            $data["status"] = true;
            $data['bank'] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract($row);
                $data['bank'] = $row;
            }
            return $data;
        }
        return array(
                    "status"=>false,
                    'message'=>'Something went wrong... please try again',
                    );
    }

    public function delete(string $id): int
    {
        $query =    "DELETE FROM ".$this->db_table." WHERE bank_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}