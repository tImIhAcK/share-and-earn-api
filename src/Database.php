<?php 

/*============================================================================
 * Generate a database connection, using the PDO connector
 * "Adding the charset to the Databse is very important for security reasons,
 *==========================================================================*/
class Database
{

	// private $db_host = "localhost";
	// private $db_name = "share_and_earn";
	// private $db_username = "root";
	// private $db_password = "";

	private $cleardb_url;
	private $cleardb_server;
	private $cleardb_username;
	private $cleardb_password;
	private $cleardb_db;
	public $conn;

	const ATTR_EMULATE_PREPARES = false;
	const ATTR_STRINGIFY_FETCHES = false;

	public function __construct()
	{
		$this->cleardb_url = parse_url(getenv('DATABASE_URL'));
		$this->cleardb_server = $this->cleardb_url["host"];
		$this->cleardb_username = $this->cleardb_url["user"];
		$this->cleardb_password = $this->cleardb_url["pass"];
		$this->cleardb_db = substr($this->cleardb_url["path"],1);
	}
	
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