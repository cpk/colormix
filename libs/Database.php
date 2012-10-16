<?php

/**
 * ---------------------------------------------------------------------------------
 * | MySQLi Database class v 1.0
 * ---------------------------------------------------------------------------------
 * @author Peter Jurkovic
 * @link www.peterjurkovic.sk
 * @version 20110715
 * 
 * Error info:
 * -1 : Invalid connection information
 * -2 : Error in type detection of value
 * 
 */


class Database 
{
    private static $instance;
    private $mysql; 
    
    /** ---------------------------------------------------------------------------------
     * Create connection to database if not exists 
     * 
     * @param String $server Server name
     * @param String $user usernam of database
     * @param String $pass password of database
     * @param String $database name of database
     * @thorws MysqlException if conecting failed of bad login information to DB
     */
    
    private function __construct($server, $user, $pass, $database){

        if(self::$instance == NULL){
            $this->mysql = new mysqli($server, $user, $pass, $database, 3306);
            if (mysqli_connect_errno()) {
               	throw new MysqlException( mysqli_connect_errno() , mysqli_connect_error());
            }

            if (!$this->mysql->query("set names 'utf8'")){
                throw new MysqlException( $this->mysql->errno, $this->mysql->error );
            }
            
        }else{
             throw new MysqlException( "-1" , "Duplicate connection.");
        }			
    }

    
    /** ---------------------------------------------------------------------------------
     * SINGLETON method
     * Create new instance of database if not exists or return self instance
     * 
     * @param String $server Server name
     * @param String $user usernam of database
     * @param String $pass password of database
     * @param String $database name of database
     * @return SELF instance 
     */
    public static function getInstance( $server, $user, $pass, $database ){

        if(self::$instance == NULL){
            self::$instance = new Database( $server, $user, $pass, $database );
        }
        return self::$instance;
    }
    
       
    
    /** ---------------------------------------------------------------------------------
    * Run a query and return stored statement
    * 
    * @param {string} SQL query with "?" params
    * @param {array} data
    * @return Mysqli statemnt
    * @throws MysqlException, if an error occured 
    */
    public function query($query , $data){
        
        $stmt = $this->mysql->stmt_init();
        
        if(!$stmt->prepare($query)){
            throw new MysqlException( $this->mysql->errno, "SQL preparing failed: ". $this->mysql->error , $query );
        }
       
        
        if($data != null && count($data) > 0 ){
            $stmtData = $this->getTypes( $data );
            $params = $this->arrayToReffArray( $stmtData["values"] );
            array_unshift( $params, implode('', $stmtData["types"]));
            call_user_func_array(array($stmt, 'bind_param'), $params);
        } 

        if(!$stmt->execute()){
            throw new MysqlException( $this->mysql->errno, "SQL execution failed: ". $this->mysql->error , $query );
        }
		//echo  $query;
		$stmt->store_result();
        return $stmt;     
    }
    
     
    
    /** ---------------------------------------------------------------------------------
     * Select data form database 
     * 
     * @param {string} SQL query with "?" params
     * @param {array} data
     * @return (array) Returned data from database, where in index = row 
     * @throws MysqlException, if an error occured 
     */
    public function select($query, $data = NULL){
        $stmt = $this->query($query , $data);
       
        
        if(!isset($stmt) || $stmt->num_rows < 1){
            return NULL;
        }
        
        $meta = $stmt->result_metadata();

        $params = array();
        while ( $field = $meta->fetch_field() ) {  
           $params[] = &$row[$field->name];  
        }  
        
        call_user_func_array(array($stmt, 'bind_result'), $params);  
	


        while ( $stmt->fetch() ) {  
            $x = array();  
            foreach( $row as $key => $val ) {  
				$x[$key] = $val;
            }  
            $results[] = $x;  
        }  

       $stmt->close();
       return $results;  
     
    }
    
    /** ---------------------------------------------------------------------------------
     *
     * @param {string} SQL query with "?" params
     * @param {array} data
     * @return (boolean) true, if query is executed successfully 
     * @throws MysqlException, if an error occured 
     */
    public function insert($query, $data = NULL ){
        $stmt = $this->query($query , $data);
        if(isset($stmt) && $stmt->affected_rows > 0 ){
			$stmt->close();
            return true;
        }
        return false;
    }
	
	/** ---------------------------------------------------------------------------------
     *
     * @param {string} SQL query with "?" params
     * @param {array} data
     * @return (boolean) true, if query is executed successfully 
     * @throws MysqlException, if an error occured 
     */
    public function update($query, $data = NULL ){
        $stmt = $this->query($query , $data);
		if(isset($stmt)){
			$stmt->close();
			return true;
		}
		return false;
    }
	
	/** ---------------------------------------------------------------------------------
     *
     * @param {string} SQL query with "?" params
     * @param {array} data
     * @return (boolean) true, if query is executed successfully 
     * @throws MysqlException, if an error occured 
     */
    public function delete($query, $data = NULL){
        return  $this->insert($query, $data);
    }
    
    
    
    /** ---------------------------------------------------------------------------------
     * Make an array of references to the values of another array
     * 
     * @param array of values
     * @return array references array
     */
    private function arrayToReffArray( &$array ){
        $reffArray = array();
        foreach($array as $key => &$val){
            $reffArray[$key] = &$val; 
        }
        return $reffArray;
    }
    
    
     /** ---------------------------------------------------------------------------------
     * Generate type of given data to String 
     * i - integer type
     * d - double type
     * s - string type 
     * 
     * @param array of values
     * @return array with index "types" with contains string witch data type and 
     * index "values" witch contains cleanded data
     */
    private function getTypes( $data ){
        $result["types"] =  array();
        $result["values"] =  array();
        
        if( count($data) > 0 ){
            
            foreach ($data as $key => $value){
                $type = gettype( $value );
                switch ($type){
                    case "boolean" :
                    case "integer" :
                        $result["types"][] = "i";
                        $result["values"][] = intval( $value );
                        break;
                    case "double" :
                        $result["types"][] = "d";
                        $result["values"][] = doubleval( $value );
                        break;
                    case "string" :
                        $result["types"][] = "s";
                        $result["values"][] = (strpos($key, "content") !== false ? $this->mysql->real_escape_string( $value ) : $value ) ;
                        break;
                    case 'NULL': 
						$result["types"][] = "s";
                        $result["values"][] =  $value;
						break;
                    case 'unknown type': 
                    default:
                        throw new MysqlException( "-2", "Error: can not detect type of key: ". $key . " - value: ".gettype($value)  );
                } // switch
                
            } // foreach
            return $result;
        } // if	
        
    } // fnc
    
    
    
    
    
    /** ---------------------------------------------------------------------------------
     * Universal method to querying to the database
     * 
     * @param String SQL with data
     * @return array if SQL query is SELECT, true otherwise
     * @throws MysqlException if query failed.
     */
    public function simpleQuery($sql)  {
        $results = array();
        if(strtolower(substr($sql, 0, strpos($sql, " "))) == "select"){
            $result = $this->mysql->query($sql);
            if ($result) {
                while ($r = $result->fetch_assoc()){
                        $results[] =  $r;
                }
                return $results;
            }else{
                throw new MysqlException( $this->mysql->errno, $this->mysql->error,  $sql);
            }
        }else{
            if ($this->mysql->query($sql)){
                return true;	
            }else{
                throw new MysqlException( $this->mysql->errno, $this->mysql->error,  $sql);
            }
        }
    }
	
	
	public function clean($str){
		return $this->mysql->real_escape_string($str);
	}
	
	public function getInsertId(){
		return $this->mysql->insert_id;
	}
        
}
