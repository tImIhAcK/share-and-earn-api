<?php 

/*============================================================================
 * Generate a database connection, using the PDO connector
 * "Adding the charset to the Databse is very important for security reasons,
 *==========================================================================*/
class Database
{

	private $db_host = "127.0.0.1";
	private $db_name = "earn_and_share";
	private $db_username = "root";
	private $db_password = "m~adeNIR21:";
	public $conn;
	public  function connect(){
		$this->conn = null;
		try{
			$this->conn = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_username, $this->db_password);
			$this->conn->exec("set names utf8");
		} catch (PDOException $e) {
			die($e->getMessage()."Couldn't connet to the database!");
		}
		return $this->conn;
	}


}


?>