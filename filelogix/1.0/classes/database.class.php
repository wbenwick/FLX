<?php

/**
 * PDO SINGLETON CLASS
 *  
 * @author Tony Landis
 * @link http://www.tonylandis.com
 * @license Use how you like it, just please don't remove or alter this PHPDoc
 */ 
class database
{  
    /**
     * The singleton instance
     * 
     */
    static private $PDOInstance; 
    private $lastQry; 
    private $lastStmt;
    private $rows;
     
  	/**
  	 * Creates a PDO instance representing a connection to a database and makes the instance available as a singleton
  	 * 
  	 * @param string $dsn The full DSN, eg: mysql:host=localhost;dbname=testdb
  	 * @param string $username The user name for the DSN string. This parameter is optional for some PDO drivers.
  	 * @param string $password The password for the DSN string. This parameter is optional for some PDO drivers.
  	 * @param array $driver_options A key=>value array of driver-specific connection options
  	 * 
  	 * @return PDO
  	 */
    public function __construct($dsn, $username=false, $password=false, $driver_options=false) 
    {
        if(!isset($PDOInstance)) { 
	        try {
			   $this->PDOInstance = new PDO($dsn, $username, $password, $driver_options);
			} catch (PDOException $e) { 
			   die("Database Connection Error: " . $e->getMessage() . "<br/>");
			}
    	}
      	return $this->PDOInstance;    	    	
    }

  	/**
  	 * Initiates a transaction
  	 *
  	 * @return bool
  	 */
	public function beginTransaction() {
		return $this->PDOInstance->beginTransaction();
	}
        
	/**
	 * Commits a transaction
	 *
	 * @return bool
	 */
	public function commit() {
		return $this->PDOInstance->commit();
	}

	/**
	 * Fetch the SQLSTATE associated with the last operation on the database handle
	 * 
	 * @return string 
	 */
    public function errorCode() {
    	return $this->PDOInstance->errorCode();
    }
    
    /**
     * Fetch extended error information associated with the last operation on the database handle
     *
     * @return array
     */
    public function errorInfo() {
    	return $this->PDOInstance->errorInfo();
    }
    
    /**
     * Execute an SQL statement and return the number of affected rows
     *
     * @param string $statement
     */
    public function exec($statement) {
    
    	error_log($this->lastQry);
    	return $this->PDOInstance->exec($statement);
    }
    
    /**
     * Retrieve a database connection attribute
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute($attribute) {
    	return $this->PDOInstance->getAttribute(constant($attribute));
    }

    /**
     * Return an array of available PDO drivers
     *
     * @return array
     */
    public function getAvailableDrivers(){
    	return $this->PDOInstance->getAvailableDrivers();
    }
    
    /**
     * Returns the ID of the last inserted row or sequence value
     *
     * @param string $name Name of the sequence object from which the ID should be returned.
     * @return string
     */
	public function lastInsertId($name) {
		return $this->PDOInstance->lastInsertId($name);
	}
        
   	/**
     * Prepares a statement for execution and returns a statement object 
     *
     * @param string $statement A valid SQL statement for the target database server
     * @param array $driver_options Array of one or more key=>value pairs to set attribute values for the PDOStatement obj 
returned  
     * @return PDOStatement
     */
    public function prepare ($statement, $driver_options=false) {
    	if(!$driver_options) $driver_options=array();
    	$this->lastQry=$statement;
    	return $this->PDOInstance->prepare($statement, $driver_options);
    }
    
    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function query($statement) {
      	$this->lastQry=$statement;
    	$this->lastStmt=$this->PDOInstance->query($statement);
		if ($this->lastStmt) {
	 		$this->rows=$this->lastStmt->rowCount();
	 	}
    	return $this->lastStmt;
    }
    
    /**
     * Executes an SQL statement for the sole purpose of confirming if a matching record exists
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function exists($statement) {
      	$this->lastQry=$statement;
    	$this->lastStmt=$this->PDOInstance->query($statement);
 		$this->rows=$this->lastStmt->rowCount();
    	if ($this->rows > 0) {
    		error_log("Exists: Found a match for $statement");
	    	return 1;
    	}
    	else {
       		error_log("Exists: Did not find a match for $statement");
	    	return 0;
    	}
    }

    
    /**
     * Executes an SQL insert statement based on parameter key/values included in function, returns the last_insert_ID
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function insert($tableName, $params = array()) {
    
    	error_log("DB Insert ($tableName).");
    
    	$valuesStr="";
    	$columnsStr=""; 	
    	 	
        foreach($params as $key => $value) {

//	        error_log("insert: $key = $value");
	        
	        if (strlen($columnsStr)>1) {
		        $columnsStr .= ",";
		        $valuesStr .= ",";
	        }
	        
            if(strlen($value)<1) {
	            $valStr="''";
            }
            else {
	            $valStr = $this->quote($value);
            }
            
            $columnsStr .= "`$key`";
            $valuesStr .= "$valStr"; 
        }

    	$statement="insert into $tableName($columnsStr) values($valuesStr)";
       	$this->lastQry=$statement;
       	$this->lastStmt=$this->PDOInstance->query($statement);
       	$this->rows=0;
       	
 		return $this->lastInsertId(null);
    }


    /**
     * Executes an SQL replace statement based on parameter key/values included in function, returns the record's ID
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function replace($tableName, $params = array()) {
    
    	$valuesStr="";
    	$columnsStr=""; 	
    	 	
        foreach($params as $key => $value) {

	        error_log("replace: $key = $value");
	        
	        if (strlen($columnsStr)>1) {
		        $columnsStr .= ",";
		        $valuesStr .= ",";
	        }
	        
            if(strlen($value)<1) {
	            $valStr="''";
            }
            else {
	            $valStr = $this->quote($value);
            }
            
            $columnsStr .= "`$key`";
            $valuesStr .= "$valStr"; 
        }

    	$statement="replace into $tableName($columnsStr) values($valuesStr)";
       	$this->lastQry=$statement;
       	$this->lastStmt=$this->PDOInstance->query($statement);
       	$this->rows=0;
       	
 		return $this->lastInsertId(null);
    }

    
   /**
     * Executes an SQL update statement based on parameter key/values included in function, on success returns 1, failure returns -1
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function update($tableName, $idColumn, $idValue, $params = array()) {
    
    	$keyValuesStr="";
    	 	
    	if (($idColumn != "") and ($idValue > 0)) {
		        foreach($params as $key => $value) {
			        if ($key != "") {
				        error_log("update: $key = $value");
				        
				        if (strlen($keyValuesStr)>1) {
					        $keyValuesStr .= ",";
				        }
				        
			            if(strlen($value)<1) {
				            $keyValuesStr .= "`$key`=''";
			            }
			            else {
				            $keyValuesStr .= "`$key`=" . $this->quote($value);
			            }
		            }
		        }
		
		       	$statement="update $tableName set $keyValuesStr where `$idColumn`='$idValue'";
		
		       	$this->lastQry=$statement;
		       	$this->lastStmt=$this->PDOInstance->query($statement);
		       	$this->rows=0;
		       	
		 		return 1;
		 }
		 else {
			 return -1;
		 }
    }

   /**
     * Executes an SQL update statement that requires an MD5 hash to be set for a specific field on success returns 1, failure returns -1
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function updateMD5($tableName, $idColumn, $idValue, $md5field, $md5value) {
    

		if (($md5field != "") and ($md5value != "")) {
		       	$statement="update $tableName set `$md5field`=md5('$md5value') where `$idColumn`='$idValue'";
		
		       	$this->lastQry=$statement;
		       	$this->lastStmt=$this->PDOInstance->query($statement);
		       	$this->rows=0;
		       	
		 		return 1;
		 }
		 else {
			 return -1;
		 }
    }
    
   /**
     * Executes an SQL delete statement based on parameter key/values included in function, on success returns 1, failure returns -1
     *
     * @param string $statement
     * @return PDOStatement
     */
    public function delete($tableName, $idColumn, $idValue, $params = array()) {
    
    	$keyValuesStr="";
    	 	
    	if (($idColumn != "") and ($idValue != "")) {
		        foreach($params as $key => $value) {
			        if ($key != "") {
				        error_log("delete: $key = $value");
				        
				        if (strlen($keyValuesStr)>1) {
					        $keyValuesStr .= " and ";
				        }
				        
			            if(strlen($value)<1) {
				            $keyValuesStr .= "`$key`=''";
			            }
			            else {
				            $keyValuesStr .= "`$key`=" . $this->quote($value);
			            }
		            }

		        }

		        if (strlen($keyValuesStr)>0) {
			            $keyValuesStrTmp = " and " . $keyValuesStr;
			            $keyValuesStr = $keyValuesStrTmp;
	            }
		
		       	$statement="delete from $tableName where `$idColumn`='$idValue' $keyValuesStr";
		
			   	error_log("DB: " . $statement);
		
		       	$this->lastQry=$statement;
		       	$this->lastStmt=$this->PDOInstance->query($statement);
		       	$this->rows=0;
		       	
		 		return 1;
		 }
		 else {
			 return -1;
		 }
    }
    
    
     /**
     * Returns row count from last query
     *
     * 
     * @return row count (integer)
     */
    public function rows() {
    	error_log("Row Count: " . $this->rows);
    	return $this->rows;
    }

     /**
     * Returns connection status
     *
     * 
     * @return row count (integer)
     */
    public function status() {
    	return $this->getAttribute("PDO::ATTR_CONNECTION_STATUS");
    }

    
    /**
     * Execute query and return all rows in assoc array
     *
     * @param string $statement
     * @return array
     */
    public function queryFetchAllAssoc($statement) {
    	return $this->PDOInstance->query($statement)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Execute query and return one row in assoc array
     *
     * @param string $statement
     * @return array
     */
    public function queryFetchRowAssoc($statement) {
    	return $this->PDOInstance->query($statement)->fetch(PDO::FETCH_ASSOC);    	
    }
    
    /**
     * Execute query and select one column only 
     *
     * @param string $statement
     * @return mixed
     */
    public function queryFetchColAssoc($statement) {
    	return $this->PDOInstance->query($statement)->fetchColumn();    	
    }
    
    /**
     * Quotes a string for use in a query
     *
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function quote ($input, $parameter_type=0) {
//    	error_log("db->quote: " . $input);
    	return $this->PDOInstance->quote($input, $parameter_type);
    }
    
    /**
     * Quotes a string for use in a query
     *
     * @param string $input
     * @param int $parameter_type
     * @return string
     */
    public function real_escape_string ($input, $parameter_type=0) {
    	return $this->PDOInstance->quote($input, $parameter_type);
    }
    
    
    /**
     * Rolls back a transaction
     *
     * @return bool
     */
    public function rollBack() {
    	return $this->PDOInstance->rollBack();
    }      
    
    /**
     * Set an attribute
     *
     * @param int $attribute
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attribute, $value  ) {
    	return $this->PDOInstance->setAttribute($attribute, $value);
    }
    
    /**
     * Returns the last query called - for debugging purposes
     *
     * @return string
     */
    
    public function lastQuery() {
    	return $this->lastQry;
    }
    
}
?>