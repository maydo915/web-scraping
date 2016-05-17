<?php

/* 
 * Connect to MySQL database for manipulating data
 * Date: 16 April 2016
 */

class Database {

    private $connection;
    private $stmt;
    
    public static $no_insert_rows;

    /**
     * Constructor - create a connection to MySQL database.
     * @param string $host Database server
     * @param string $database MySQL database
     * @param strin  $username Database username
     * @param string $password User's password
     */

    public function __construct() {
        // Local server
        $host = 'localhost';
        $username = 'maytdo';
        $password = 'password1';
        $database = 'realestate_db';
        
        try{
            // Connect to database
            $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);

            // Set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        } catch (PDOException $ex) {
            
            // Should customise the errors
            echo '<strong>Error in database connection. ' .$ex->getMessage(). '</strong>';
        }
        
    }
   
    /**
     * Prepare the query
     * @param string $sql   
     */
    public function query($sql){
        $this->stmt = $this->connection->prepare($sql);
    }
    // The function should be replaced query($sql) function
    public function prepareQuery($sql){
        $this->stmt = $this->connection->prepare($sql);
    }


    /**
     * Bind the statement, binds inputs with the placeholders
     * @param string $param
     * @param string $value
     * @param boolean $type
     */
    public function bind($param, $value, $type=null){
        $this->stmt->bindValue($param, $value, $type);
    }
    
    /**
     * Execute the query
     * @return PDO object
     */
    public function execute(){
        return $this->stmt->execute();
    }

    /**
     * Return a set of rows in an associated array
     * @return array Associate array
     */
    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    // Return number of record be effective
    public function rowCount(){
        self::$no_insert_rows++ ;
    }

    
    // Not working
    public function lastInsertID(){
        return $this->connection->lastInsertId();
    }

     
    /**
     * Insert multiple records at a time
     */
    public function beginTransaction(){
        $this->connection->beginTransaction();
    }

    /**
     * End transaction and commit changes
     */
    public function endTransaction(){
        $this->connection->commit();
    }

    /**
     * Get the PDO connections
     */
    public function getConnection(){
        return $this->connection;
    }
    
    /**
     * Check whether database connection exists
     * @return boolean
     */
    public function isConnected(){
        if($this->connection){
            return TRUE;
        }
    }
}

