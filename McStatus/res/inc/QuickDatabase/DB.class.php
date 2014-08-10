<?php
require_once 'Config.php';
require_once 'DBResult.class.php';

class DB {
   
    const DB_MYSQL  = 'MYSQL';
    
    private static $_instance = null;
    private $_pdo,
            $_pdo_schema,
            $_pdo_type,
            $_query,
            $_error         = false,
            $_error_message = null,
            $_results,
            $_count         = 0,
            
            $_operators     = array(
                '=',
                '>',
                '<',
                '>=',
                '<=',
                '!='
            );
    
    private $_pdo_host,
            $_pdo_name,
            $_pdo_user,
            $_pdo_pass;
    
    private function __construct($dbType) {
        try {
            $this->_pdo_type = $dbType;
            switch ($dbType) {
                
                case self::DB_MYSQL:
                    $this->_pdo_host = Config::get('mysql/host');
                    $this->_pdo_name = Config::get('mysql/db');
                    $this->_pdo_user = Config::get('mysql/username');
                    $this->_pdo_pass = Config::get('mysql/password');
                    $this->_pdo = new PDO('mysql:host=' . $this->_pdo_host . ';dbname=' . $this->_pdo_name, $this->_pdo_user, $this->_pdo_pass);
                    $this->_pdo_schema = new PDO('mysql:host=' . $this->_pdo_host . ';dbname=INFORMATION_SCHEMA', $this->_pdo_user, $this->_pdo_pass);
                    break;             
                
                default:
                    die('Unknown Database-Type.');
                    break;
            }
          
        } catch (PDOException $ex) {
            $this->_error = true;
            $this->_error_message = $ex->getMessage();
            die($ex->getMessage());
        }
    }
    
    /**
     * Gibt eine Instanz der DB-Klasse zurueck, falles noch keine existiert wird eine neue erstellt.
     * @return \DB
     */
    public static function getInstance() {
        
        if (!(isset(self::$_instance))) {
            
            $type = null;
        
            if(Config::get('mysql/use') === true) {
                $type = self::DB_MYSQL;
            }
        
            self::$_instance = new DB($type);
        }
        
        return self::$_instance;
    }
    
    /**
     * Gibt den DBType zurück.
     * @return type
     */
    public function getType() {
        return $this->_pdo_type;
    }
    
    /**
     * Führt einen PDO-Query aus.
     * @param String $sql Der SQL-Befehl.
     * @param Array $params Die Parameter fuer die Prepared-Statements.
     * @return \DB
     */
    public function query($sql, $params = array()) {
        $this->_error = false;
        $this->_error_message = null;
        
        $this->_query = $this->_pdo->prepare($sql);

        $x = 1;
        if (count($params)) {
            foreach ($params as $param) {
                $this->_query->bindValue($x, $param);
                $x++;
            }
        }
        if ($this->_query->execute()) {
            $this->_results = $this->_query;
            $this->_count = $this->_query->rowCount();
        } else {
            $this->_error = true;
            $this->_error_message = $this->_query->errorInfo();
        }
        
        return $this;
    }
    
    private function action($action, $table, $where = array()) {
        $sql = '';
        
        if (count($where) === 3) {

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if (in_array($operator, $this->_operators)) {
                $sql = "{$action} FROM `{$table}` WHERE `{$field}` {$operator} ?";
            }
        } else {
            $sql = "{$action} FROM `{$table}`";
        }
        
        if(count($where)) {
            if (!($this->query($sql, array($value))->error())) {
                return $this;
            }
        }else {
            if (!($this->query($sql)->error())) {
                return $this;
            }
        }
        
        return false;
    }
    
    /**
     * Damit kann man die Eintraege in einer Datenbank zaehlen.
     * @param String $table Der Name der Tabelle.
     * @param Array $where Ein Array, der Informationen für den Where-Query enthält.
     * @return Integer
     */
    public function count($table, $where = array()) {
        return $this->action('SELECT COUNT(*) AS `countedValue`', $table, $where)->_query->fetchAll(PDO::FETCH_OBJ)[0]->countedValue;
    }
    
    /**
     * @deprecated 
     */
    public function get($table, $where = array()) {
        trigger_error('Deprecated function called use the new select($table, $fetchMethod = PDO::FETCH_OBJ, $where = array()) method.', E_USER_NOTICE);
        return $this->action('SELECT *', $table, $where)->_query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Zum selektieren von Daten aus einer Datenbank.
     * @param String $table Der Name der Tabelle.
     * @param PDO $fetchMethod Die Methode die zum fetchen der Daten verwendet werden soll.
     * @param Array $where Ein Array, der Informationen für den Where-Query enthält.
     * @return \DBResult
     */
    public function select($table, $fetchMethod = PDO::FETCH_OBJ, $where = array()) {
        return new DBResult($this->action('SELECT *', $table, $where)->_query, (is_null($fetchMethod) ? PDO::FETCH_OBJ : $fetchMethod));
    }
    
    /**
     * Eine Methode die jeden Query ausführt.
     * @param String $query Der Query.
     * @param Array $params Die Parameter die im String eingebunden werden sollen.
     * @param PDO $fetchMethod 
     * @return \DBResult
     */
    public function databaseQuery($query, $params = array(), $fetchMethod = PDO::FETCH_OBJ) {
        return new DBResult($this->query($query, $params)->_query, $fetchMethod);
    }
    
    /**
     * Loescht Eintraege in einer Tabelle.
     * @param String $table Der Name der Tabelle.
     * @param Array $where Ein Array, der Informationen für den Where-Query enthält.
     * @return \DB
     */
    public function delete($table, $where = array()) {
        return $this->action('DELETE', $table, $where);
    }
    
    /**
     * Zum eintragen von Daten in eine Tabelle.
     * @param String $table Der Name der Tabelle.
     * @param String $fields Ein Array, der die Felder enthält, die eingetragen werden sollen.
     * @return boolean
     */
    public function insert($table, $fields = array()) {
        $keys   = array_keys($fields);
        $values = null;
        $x      = 1;
        foreach($fields as $field) {
            $values .= '?';
                
            if($x < count($fields)) {
                $values .= ', ';
            }

            $x++;
        }

        $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
        if(!($this->query($sql, $fields)->error())) { 
            return true;   
        }

        return false;
    }
    
    /**
     * Zum aendern von Daten in einer Tabelle.
     * @param String $table Der Name der Tabelle.
     * @param Array $fields Ein Array, der die Felder beinhält, die geändert werden sollen.
     * @param Array $where Ein Array, der Informationen für den Where-Query enthält.
     * @return boolean
     */
    public function update($table, $fields = array(), $where = array()) {
        $set    = '';
        $x      = 1;

        foreach ($fields as $name => $value) {
            $set .= "`{$name}` = ?";

            if($x < count($fields)) {
                $set .= ', ';
            }

            $x++;
        }
        
        $sql = '';
        
        if(count($where) === 3) {

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];
            
            if(in_array($operator, $this->_operators)) {
                $sql = "UPDATE `{$table}` SET {$set} WHERE {$field} {$operator} '{$value}'";
            }
        } else {
            $sql = "UPDATE `{$table}` SET {$set}";
        }
        
        if(!($this->query($sql, $fields)->error())) {
            return true;
        }
 
        return false;
    }
    
    /**
     * Mit dieser Methode kann man eine Tabelle erstellen.
     * @param String $table Der Name der Tabelle.
     * @param Array $fields Die Felder die die Tabelle beinhalten soll.
     * @param Boolean $notExists Falls eine Tabelle berereits existiert wird diese nicht überschrieben, wenn der Wert auf 'false' steht.
     * @return \DB
     */
    public function createTable($table, $fields = array(), $notExists = false) {
        
        $set = '';
        $x = 1;
        
        foreach ($fields as $name => $values) {
            
            $data   = array(
                'NAME'          => (is_array($values) ? $name : $fields[$name]),
                'TYPE'          => 'VARCHAR',
                'LENGTH'        => '255',
                'NOT_NULL'      => false,
                'PRIMARY'       => false,
                'AUTO_INCREMENT'=> false,
                'KEY'           => false
            ); 
            
            if(is_array($values) && count($values)) {
                
                foreach($values as $option => $value) {
                    
                    switch(strtoupper($option)) {
                        
                        case 'NOT_NULL':
                            $data['NOT_NULL'] = $value;
                            break;
                        
                        case 'PRIMARY':
                            $data['PRIMARY'] = $value;
                            break;
                        
                        case 'AUTO_INCREMENT':
                            $data['AUTO_INCREMENT'] = $value;
                            break;
                        
                        case 'KEY':
                            $data['KEY'] = $value;
                            break;
                        
                        case 'TYPE':
                            $data['TYPE'] = $value;
                            break;
                    
                        case 'LENGTH':
                            $data['LENGTH'] = "{$value}";
                            break;
                    }
                    
                } 
                
            }
            
            $set .= "{$data['NAME']} {$data['TYPE']}({$data['LENGTH']})"
                    . ($data['NOT_NULL'] ? " NOT NULL" : "")
                    . ($data['PRIMARY'] ? " PRIMARY" : "")
                    . ($data['AUTO_INCREMENT'] ? " AUTO_INCREMENT" : "")
                    . ($data['KEY'] ? " KEY" : "");

            if($x < count($fields)) {
                $set .= ', ';
            }

            $x++;
        }
        
        $sql = "CREATE TABLE" . ($notExists ? ' IF NOT EXISTS' : '') . " `{$table}` (" . $set . ")";
        $this->query($sql);
        
        return $this;
    }
    
    /**
     * Mit dieser Methode kann man eine Tabelle loeschen.
     * @param String $table Der Name der Tabelle.
     * @return \DB
     */
    public function dropTable($table) {
        return $this->query("DROP TABLE `{$table}`");
    }
    
    /**
     * Zum optimieren von Tabellen kann diese Methode aufgerufen werden.
     * @param String $table Der Name der Tabelle.
     * @return \DB
     */
    public function optimizeTable($table) {
        return $this->query("OPTIMIZE TABLE `{$table}`");
    }
    
    /**
     * Zum reparieren von Tabellen kann diese Methode aufgerufen werden.
     * @param String $table Der Name der Tabelle.
     * @return \DB
     */
    public function repairTable($table) {
        return $this->query("REPAIR TABLE `{$table}`");
    }
    
    /**
     * Mit dieser Methode kann man prüefen, ob eine Datenbank existiert.
     * @param String $databaseName Der Name der Datenbank.
     * @return boolean
     */
    public function existsDatabase($databaseName) {
        return ($this->_pdo_schema->query("SELECT `TABLE_NAME` FROM `tables` WHERE `table_schema` = '{$databaseName}'")->rowCount() > 0);
    }
    
    /**
     * Mit dieser Methode kann man prüefen, ob eine Tabelle existiert.
     * @param String $tableName Der Name der Tabelle.
     * @return Boolean
     */
    public function existsTable($tableName) {
        return ($this->_pdo->query("SHOW TABLES LIKE '{$tableName}'")->rowCount() > 0);
    }
    
    public function getField($table, $field, $fetchMethod = PDO::FETCH_OBJ) {
        return new DBResult($this->_pdo->query("SELECT `{$field}` FROM `{$table}`"), $fetchMethod);
    }
    
    /**
     * Dont use this method! In work!
     * @param type $fields
     * @param type $on
     * @param type $where
     * @param type $fetchMethod
     * @return boolean|\DBResult
     */
    public function joinSelect($fields = array(), $on = array(), $where = array(), $fetchMethod = PDO::FETCH_OBJ) {
        $sql = 'SELECT ';
        
        if(!(is_array($fields)) || !(count($fields))) {
            return false;
        }
        
        $x = 0;
        foreach($fields as $table) {
            
            $tableName = array_keys($fields)[$x];
            
            $i = 1;
            foreach($table as $field => $as) {
                $sql .= "`{$tableName}`.`"
                     . (is_int($field) ? $as : $field . "`")
                     . (is_int($field) ? "`" : " AS `{$as}`");
                
                if($i < count($table)) {
                    $sql .= ', ';
                }
                
                $i++;
            }
            
            $x++;
            
            if($x < count($fields)) {
                $sql .= ', ';
            }
        }
        
        $sql .= ' FROM ';
        
        $z = 0;
        foreach($fields as $table) {
            $sql .= '`' . array_keys($fields)[$z] . '`';
            
            $z++;
            if($z < count($fields)) {
                $sql .= ', ';
            }
        }
        
        if(is_array($on) && count($on) === 3) {
            
            $joinTable = array_keys($on)[0];
            $joinField = $on[$joinTable];
            
            $table = array_keys($on)[1];
            $field = $on[$table];
            
            $operator = $on[0];
            
            if(in_array($operator, $this->_operators)) {
                $sql .= " LEFT JOIN `{$joinTable}` ON `{$table}`.`{$field}` {$operator} `{$joinTable}`.`{$joinField}`";
            }
            
        }
        
        if(count($where) === 3) {
            
                $field      = $where[0];
                $operator   = $where[1];
                $value      = $where[2];
                
                if(in_array($operator, $this->_operators)) {
                    $sql .= " WHERE `{$field}` {$operator} '{$value}'";
                }
        }
        
        return new DBResult($this->query($sql)->_query, $fetchMethod);
    }
    
    /**
     * Gibt zurueck ob es einen Error gab.
     * @return boolean
     */
    public function error() {
        return $this->_error;
    }
    
    /**
     * Gibt Informationen ueber einen Error zurueck.
     * @return Array
     */
    public function getErrorMessage() {
        return $this->_error_message;
    }

}
?>