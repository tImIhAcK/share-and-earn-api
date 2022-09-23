<?php
class Order 
{
    private $conn;
    // Table
    private $db_table = "orders";
    // Columns
    // public $order_id;
    public $order_no;
    public $order_type;
    public $order_quantity;
    public $order_price;
    public $order_owner;
    public $user_id;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM orders";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = array();
        $data['total user'] = $stmt->rowCount();
        $data['body'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $orderArr = $row;
            array_push($data["body"], $orderArr);
        }
        return $data;

    }

    public function create($data): array
    {
        $query =    "INSERT INTO
                    ". $this->db_table ."
                    SET
                    order_no=:order_no, 
                        order_type=:order_type,
                        order_quantity=:order_quantity,
                        order_price=:order_price,
                        order_owner=:order_owner";
        
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->order_no=date("YmdHis")."".random_int(1000, 9999);
        $this->order_type=htmlspecialchars(strip_tags($data->order_type));
        $this->order_quantity=htmlspecialchars(strip_tags($data->order_quantity));
        $this->order_price=htmlspecialchars(strip_tags($data->order_price));
        $this->order_owner=htmlspecialchars(strip_tags($data->order_owner));
        // $this->order_id=strtoupper(bin2hex(random_bytes(8)));

    
        // bind data
        $stmt->bindValue(":order_no", $this->order_no, PDO::PARAM_STR);
        $stmt->bindValue(":order_type", $this->order_type, PDO::PARAM_STR);
        $stmt->bindValue(":order_quantity", $this->order_quantity, PDO::PARAM_INT);
        $stmt->bindValue(":order_price", $this->order_price, PDO::PARAM_INT);
        $stmt->bindValue(":order_owner", (int)$this->order_owner, PDO::PARAM_INT);
    
        if($stmt->execute()){
            $data = array();
            $data["status"] = true;
            $data['body'] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $orderArr = array(
                    "order_no" => $order_no,
                    "order_type" => $order_type,
                    "order_quantity"=>$order_quantity,
                    "order_price"=>$order_price
                );
                array_push($data["body"], $orderArr);
            }
            return $data;
        }
        return array(
                    "status"=>false,
                    'message'=>'Something went wrong when making order... please try again',
                    );
    }

    public function get(string $id): array | false
    {
        $query = "SELECT * FROM ".$this->db_table." WHERE order_owner=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data = $row;
        }
        return $data;
    }


    public function delete(string $id): int
    {
        $query =    "DELETE FROM ".$this->db_table." WHERE order_owner=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}