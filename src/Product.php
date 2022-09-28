<?php

include "../vendor/autoload.php";
use Firebase\JWT\JWT;

class Product 
{
    private $conn;
    // Table
    private $db_table = "products";
    // Columns
    public $prod_id;
    public $prod_name;
    public $prod_price;
    public $prod_desc;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(): array
    {
        // /**
        //  * To get all the products from the database
        //  * @param - 
        //  * @return - associative array of product
        //  */
        $query = "SELECT * FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = array();
        $data['total product'] = $stmt->rowCount();
        $data['products'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $prodArr = array(
                "prod_id" => $prod_id,
                "prod_name" => $prod_name,
                "prod_price"=>$prod_price,
                "prod_desc"=>$prod_desc
            );
            array_push($data["products"], $prodArr);
        }
        return $data;
    }

    public function get(string $id): array | false
    {
        $query = "SELECT * FROM ".$this->db_table." WHERE prod_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = array();
        $data['prod'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $data['prod'] = $row;

        }
        return $data;
    }

    public function create($data): array
    {
        $query =    "INSERT INTO
                    ". $this->db_table ."
                    SET
                    prod_name=:prod_name,
                    prod_price=:prod_price,
                    prod_desc=:prod_desc";
        
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->prod_name=htmlspecialchars(strip_tags($data->prod_name));
        $this->prod_price=htmlspecialchars(strip_tags($data->prod_price));
        $this->prod_desc=htmlspecialchars(strip_tags($data->prod_sc));

    
        // bind data
        $stmt->bindValue(":prod_name", $this->prod_name, PDO::PARAM_STR);
        $stmt->bindValue(":prod_price", $this->prod_price, PDO::PARAM_INT);
        $stmt->bindValue(":prod_desc", $this->prod_desc, PDO::PARAM_STR);
    
        if($stmt->execute()){
            $data = array();
            $data["status"] = true;
            $data['prod'] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                // extract($row);
                $data['prod'] = $row;
            }
            return $data;
        }
        return array(
                    "status"=>false,
                    'message'=>'Something went wrong... please try again',
                    );
    }

public function update(array $current, array $new): int
    {
        $query =    "UPDATE
                    ". $this->db_table ."
                    SET
                        prod_name=:prod_name,
                        prod_price=:prod_price,
                        prod_desc=:prod_desc
                    WHERE
                        user_id=:id";
        
        $stmt = $this->conn->prepare($query);
    
        // bind data
        $stmt->bindValue(":prod_name", $new['prod_name'] ?? $current['prod_name'], PDO::PARAM_STR);
        $stmt->bindValue(":prod_price", $new['prod_price'] ?? $current['prod_price'], PDO::PARAM_STR);
        $stmt->bindValue(":prod_desc", $new['prod_desc'] ?? $current['prod_desc'], PDO::PARAM_INT);
    
        $stmt->execute();
        return $stmt->rowCount();

    }

    public function delete(string $id): int
    {
        $query =  "DELETE FROM ".$this->db_table." WHERE prod_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}