<?php

class Conexion{
	
	private $pdo;

	public function __construct(){
        $config = parse_ini_file('config.ini');
		$options = [
		              PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
		              PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
		              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC //make the default fetch be an associative array
		            ];
		try {
		    $this->pdo  = new PDO("mysql:host=".$config["servername"].";dbname=".$config["dbname"].";charset=utf8",   $config['username'],  $config['password'], $options);
		} catch (\PDOException $e) {
		     throw new \PDOException($e->getMessage(), (int)$e->getCode());
		}
        
    }


    public function lastInsertId(){
        return $this->pdo->lastInsertId();
    }

	public function executeSql($query, $params = null){
        $resultSet = null;
        
        try{
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            if($stmt){
                $row = $stmt->fetchObject();
                
                if (!$row){
                    $resultSet = [];
                }
                else{
                    while($row) {
                        $resultSet[] = $row;
                        $row = $stmt->fetchObject();
                    }
                }
            }else{  
                $error = $stmt->errorInfo();
                printf("Errormessage in query: %s:\n %s\n", $stmt, print_r($error));
                $resultSet = false;
            }
            
        } catch(PDOException $e) {
            printf("Errormessage in query: %s:\n %s\n", $query, $e->getMessage());
        }
        
        return $resultSet;
    }

}

?>