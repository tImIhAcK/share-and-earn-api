<?php

class Category
{
    private $conn;
    // Table
    private $db_table = "category";
    // Columns
    public $id;
    public $name;
    public $status;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }
    // GET ALL
    public function getCategory(){
        $sqlQuery = "SELECT id, name, status FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }
}