<?php 

/*============================================================================
 * Generate a database connection, using the PDO connector
 * "Adding the charset to the Databse is very important for security reasons,
 *==========================================================================*/
class Database
{

	// private $db_host = "localhost";
	// private $db_name = "earn_and_share";
	// private $db_username = "root";
	// private $db_password = "m~adeNIR21:";

	private $cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
	private $cleardb_server = $this->cleardb_url["host"];
	private $cleardb_username = $this->cleardb_url["user"];
	private $cleardb_password = $this->cleardb_url["pass"];
	private $cleardb_db = substr($this->cleardb_url["path"],1);
	public $conn;

	const ATTR_EMULATE_PREPARES = false;
	const ATTR_STRINGIFY_FETCHES = false;
	
	public  function connect(){
		$this->conn = null;
		try{
			$this->conn = new PDO("mysql:host=" . $this->cleardb_server . ";dbname=" . $this->cleardb_db, $this->cleardb_username, $this->cleardb_password);
			$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, PDO::ATTR_STRINGIFY_FETCHES);
			$this->conn->exec("set names utf8");
		} catch (PDOException $e) {
			echo json_encode(array("message", $e->getMessage()."Couldn't connet to the database!"));
		}
		return $this->conn;
	}


}


?>