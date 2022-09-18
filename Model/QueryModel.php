<?php

require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class QueryModel extends Database
{

	// Function to query the database
	public static function query($query, $params = array()){
		$statement = parent::connect()->prepare($query);
		$statement->execute($params);

		if (explode(' ', $query)[0] == 'SELECT') {
			$data = $statement->fetchAll();

			try{
				return $data;
			}catch(Exception $e){
				return $e->getMessage()."Couldn't fetch data form the database! ";
			}
		}
	
	}

}