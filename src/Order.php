<?php
class Order 
{
    private $conn;
    // Table
    private $db_table = "orders";
    // Columns
    // public $order_id;
    public $order_no;
    public $order_prod;
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
        $data['order'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // extract($row);
            $data['order'] = $row;
            
        }
        return $data;

    }

    public function create($data): array
    {
        $query =    "INSERT INTO
                    ". $this->db_table ."
                    SET
                    order_no=:order_no, 
                        order_prod=:order_prod,
                        order_quantity=:order_quantity,
                        order_price=:order_price,
                        user_id=:user_id";
        
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->order_no=date("YmdHis")."".random_int(1000, 9999);
        $this->order_prod=htmlspecialchars(strip_tags($data->order_prod));
        $this->order_quantity=htmlspecialchars(strip_tags($data->order_quantity));
        $this->order_price=htmlspecialchars(strip_tags($data->order_price));
        $this->user_id=htmlspecialchars(strip_tags($data->user_id));
        // $this->order_id=strtoupper(bin2hex(random_bytes(8)));

    
        // bind data
        $stmt->bindValue(":order_no", $this->order_no, PDO::PARAM_STR);
        $stmt->bindValue(":order_prod", $this->order_prod, PDO::PARAM_STR);
        $stmt->bindValue(":order_quantity", $this->order_quantity, PDO::PARAM_INT);
        $stmt->bindValue(":order_price", $this->order_price, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", (int)$this->user_id, PDO::PARAM_INT);
    
        if($stmt->execute()){
            $data = array();
            $data["status"] = true;
            $data['order'] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $orderArr = array(
                    "order_no" => $order_no,
                    "order_prod" => $order_prod,
                    "order_quantity"=>$order_quantity,
                    "order_price"=>$order_price
                );
                array_push($data["order"], $orderArr);
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
        $query = "SELECT * FROM ".$this->db_table." WHERE user_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
        $data['order'] = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data['order'] = $row;
        }
        return $data;
    }


    public function delete(string $id): int
    {
        $query = "DELETE FROM ".$this->db_table." WHERE order_owner=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}