<?php 

/*============================================================================
 * Generate a database connection, using the PDO connector
 * "Adding the charset to the Databse is very important for security reasons,
 *==========================================================================*/
class Database
{

	const DB_HOST = '127.0.0.1:3306';
	const DB_NAME = 'ubuy_schema';
	const DB_USER = 'root';
	const DB_PASS = 'm~adeNIR21:';

	protected static function connect(){
		$dsn = 'mysql:host='. self::DB_HOST .';dbname='. self::DB_NAME . ';charset=utf8';
		$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try {
			return $pdo;
		} catch (PDOException $e) {
			die($e->getMessage().MESSAGE_DATABASE_ERROR);
		}

	}


}


?>