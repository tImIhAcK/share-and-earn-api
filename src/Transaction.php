<?php

include "../vendor/autoload.php";
use Firebase\JWT\JWT;

class Transaction
{
    private $conn;
    // Table
    private $db_table = "transactions";
    // Columns
    public $id;
    public $trans_type;
    public $trans_amt;
    public $user_id;
    public $trans_status;
    public $trans_date;

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
        $data['transaction'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $transArr = [
                'user_id'=>$user_id,
                'trans_type'=>$trans_type,
                'trans_amt'=>$tran_samt,
                'trans_status'=>$trans_status,
                'trans_date'=>$trans_date
            ];
            array_push($data['transactions'], $transArr);
        }

        if(empty($data)){
            return array("status"=>0, 'message'=>'No transactions');
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
        $data['transaction'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $transArr = [
                'user_id'=>$user_id,
                'trans_type'=>$trans_type,
                'trans_amt'=>$tran_samt,
                'trans_status'=>$trans_status,
                'trans_date'=>$trans_date
            ];
            array_push($data['transactions'], $transArr);
        }

        if(empty($data)){
            return array("status"=>0, 'message'=>'No transactions');
        }
        return $data;
    }

    public function create($data): array
    {
        $query =    "INSERT INTO
                        ". $this->db_table ."
                    SET
                        trans_type=:trans_type,
                        trans_amt=:trans_amt,
                        user_id=:user_id,
                        trans_status=:trans_status";
        
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->trans_type=htmlspecialchars(strip_tags($data->trans_type));
        $this->trans_amt=htmlspecialchars(strip_tags($data->trans_amt));
        $this->user_id=htmlspecialchars(strip_tags($data->id));
        $this->trans_status=htmlspecialchars(strip_tags($data->trans_status));
        // $this->trans_date=htmlspecialchars((strip_tags($data->)))

    
        // bind data
        $stmt->bindValue(":trans_type", $this->trans_type, PDO::PARAM_STR);
        $stmt->bindValue(":trans_amt", $this->trans_amt, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(":trans_status", (int)$this->trans_status, PDO::PARAM_STR);
    
        if($stmt->execute()){
            $data = array();
            $data['transaction'] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract($row);
                $data['transaction'] = $row;
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
        $query = "DELETE FROM ".$this->db_table." WHERE user_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}