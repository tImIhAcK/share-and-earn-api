<?php

class Product
{
    private $conn;
    // Table
    private $db_table = "product";
    // Columns
    public $id;
    public $name;
    public $description;
    public $price;
    public $image;
    public $status;
    public $category_id;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }
    // GET ALL
    public function getProduct(){
        $query = "SELECT c.name as category_name, p.id, p.name, p.description, p.price, p.image, p.status, p.category_id 
        FROM " . $this->db_table . " p LEFT JOIN category c
	    ON p.category_id = c.id ORDER BY p.added_on DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

     public function addProduct(){
        $query = "INSERT INTO
                    ". $this->db_table ."
                SET
                    category_id = :category_id,
                    name = :name, 
                    description = :description, 
                    price = :price, 
                    image = :image,
                    status = :status";
    
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->price=htmlspecialchars(strip_tags($this->price));
        $this->image=htmlspecialchars(strip_tags($this->image));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        $this->status=htmlspecialchars(strip_tags($this->status));
    
        // bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":status", $this->status);
    
        if($stmt->execute()){
            return true;
        }
        return false;
     }


    // used when filling up the update product form
     public function getOneProduct(){
        $query = "SELECT
                        id, 
                        name, 
                        description, 
                        image, 
                        price,
                        status, 
                        category_id
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       id = ?
                    LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->name = $dataRow['name'];
        $this->description = $dataRow['description'];
        $this->image = $dataRow['image'];
        $this->price = $dataRow['price'];
        $this->status = $dataRow['status'];
        $this->category_id = $dataRow['category_id'];
     }

            // UPDATE
    public function updateProduct(){
        $query = "UPDATE
                    ". $this->db_table ."
                SET
                    name = :name, 
                    description = :description, 
                    image = :image, 
                    price = :price, 
                    status = :status,
                    category_id = :category_id,

                WHERE 
                    id = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->image=htmlspecialchars(strip_tags($this->image));
        $this->price=htmlspecialchars(strip_tags($this->price));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    
        // bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":category_id", $this->category_id);
    
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // DELETE
    function deleteProduct(){
        $query = "DELETE FROM " . $this->db_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
    
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        $stmt->bindParam(1, $this->id);
    
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    function search($keywords){

	    $keywords=htmlspecialchars(strip_tags($keywords));
	   // $keywords = "%{$keywords}%";
	 
	    // select all query
	    $query = "SELECT
	                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.added_on
	            FROM
	                " . $this->db_table . " p
	                LEFT JOIN
	                    category c
	                        ON p.category_id = c.id
	            WHERE
	                p.name LIKE '%{$keywords}%' OR p.description LIKE '%{$keywords}%' OR c.name LIKE '%{$keywords}%'
	            ORDER BY
	                p.created DESC";
	 
	    // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->description=htmlspecialchars(strip_tags($this->description));
        $this->price=htmlspecialchars(strip_tags($this->price));
        $this->added_on=htmlspecialchars(strip_tags($this->added_on));
        $this->category_id=htmlspecialchars(strip_tags($this->category_id));
    
        // bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":added_on", $this->added_on);
        $stmt->bindParam(":category_id", $this->category_id);
    
        if($stmt->execute()){
            return true;
        }
        return false;
	}
}