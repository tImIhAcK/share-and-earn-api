<?php 

/*============================================================================
 * Generate a database connection, using the PDO connector
 * "Adding the charset to the Databse is very important for security reasons,
 *==========================================================================*/
class Database
{

	private $db_host = "localhost";
	private $db_name = "id19615868_share_and_earn";
	private $db_username = "id19615868_root";
	private $db_password = "m~adeNIR21:m~adeNIR21:";
	public $conn;

	const ATTR_EMULATE_PREPARES = false;
	const ATTR_STRINGIFY_FETCHES = false;
	
	public  function connect(){
		$this->conn = null;
		try{
			$this->conn = new PDO("mysql:host=" . $this->db_host . ";dbname=" . $this->db_name, $this->db_username, $this->db_password);
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, PDO::ATTR_STRINGIFY_FETCHES);
			$this->conn->exec("set names utf8");
		} catch (PDOException $e) {
			echo json_encode(array("message", $e->getMessage()."Couldn't connet to the database!"));
		}
		return $this->conn;
	}


}