<?php
class Order 
{
    private $conn;
    // Table
    private $db_table = "orders";
    // Columns
    public $id;
    public $fullname;
    public $email;
    public $phone_number;
    public $password;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getOrders(){
        $query = "SELECT * FROM" .$this->db_table. "ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getUserOrder(){
        $query = "SELECT * FROM" .$this->db_table. "WHERE owner_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function createOrder(){
        $query = "INSERT INTO
                    ". $this->db_table ."
                SET
                    user_fullname=:user_fullname,
                    user_email=:user_email,
                    user_address=:user_address, 
                    product_id=:product_id, 
                    product_name=:product_name, 
                    price=:price,
                    total=:total,
                    owner_id=:owner_id,
                    order_id=:order_id";
    
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->fullname=htmlspecialchars(strip_tags($this->fullname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->address=htmlspecialchars(strip_tags($this->address));
        $this->product_id=htmlspecialchars(strip_tags($this->product_id));
        $this->product_name=htmlspecialchars(strip_tags($this->product_name));
        $this->price=htmlspecialchars(strip_tags($this->price));
        $this->total=htmlspecialchars(strip_tags($this->total));
        $this->owner_id=htmlspecialchars(strip_tags($this->owner_id));
        $this->order_id=strtoupper(bin2hex(random_bytes(8)));
    
        // bind data
        $stmt->bindParam(":user_fullname", $this->user_fullname);
        $stmt->bindParam(":user_email", $this->email);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":product_name", $this->product_name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":owner_id", $this->owner_id);
        $stmt->bindParam(":order_id", $this->order_id);
    
        if($stmt->execute()){
            return true;
        }
        return false;
    }

}