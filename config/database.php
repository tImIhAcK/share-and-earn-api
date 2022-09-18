<?php 

/*============================================================================
 * Generate a database connection, using the PDO connector
 * "Adding the charset to the Databse is very important for security reasons,
 *==========================================================================*/
class Database
{

	private $host = "127.0.0.1";
	private $database_name = "ubuy_schema";
	private $username = "root";
	private $password = "m~adeNIR21:";
	public $conn;
	public  function connect(){
		$this->conn = null;
		try{
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database_name, $this->username, $this->password);
			$this->conn->exec("set names utf8");
		} catch (PDOException $e) {
			die($e->getMessage()."Couldn't connet to the database!");
		}
		return $this->conn;
	}


}


?>