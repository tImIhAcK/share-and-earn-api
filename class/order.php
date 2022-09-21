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

    public function getOrders(){
        $query = "SELECT * FROM " .$this->db_table. "";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getUserOrder(){
        $query = "SELECT * FROM " .$this->db_table. " WHERE order_owner=:user_id";
        $stmt = $this->conn->prepare($query);

        $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        $stmt->bindParam(":user_id", $this->user_id);

        $stmt->execute();
        return $stmt;
    }

    public function createOrder(){
        $query = "INSERT INTO
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
        $this->order_type=htmlspecialchars(strip_tags($this->order_type));
        $this->order_quantity=htmlspecialchars(strip_tags($this->order_quantity));
        $this->order_price=htmlspecialchars(strip_tags($this->order_price));
        $this->order_owner=htmlspecialchars(strip_tags($this->order_owner));
        // $this->order_id=strtoupper(bin2hex(random_bytes(8)));

    
        // bind data
        $stmt->bindParam(":order_no", $this->order_no);
        $stmt->bindParam(":order_type", $this->order_type);
        $stmt->bindParam(":order_quantity", $this->order_quantity);
        $stmt->bindParam(":order_price", $this->order_price);
        $stmt->bindParam(":order_owner", $this->order_owner);
    
        if($stmt->execute()){
            return true;
        }
        return false;
    }

}