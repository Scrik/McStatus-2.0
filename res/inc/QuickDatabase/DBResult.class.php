<?php
class DBResult {
    
    private $_result,
            $_fetch_type;
    
    public function __construct(PDOStatement $result, $fetchType) {
        $this->_result = $result;
        $this->_fetch_type = $fetchType;
    }
    
    /**
     * Gibt einen Array zurueck, der alle Resultate enthält.
     * @return Array
     */
    public function getResult() {
        return $this->_result->fetchAll($this->_fetch_type);
    }
    
    /**
     * Anzahl der Spalten, des Resultats.
     * @return Integer
     */
    public function columnCount() {
        return $this->_result->columnCount();
    }
    
    /**
     * Anzahl der Zeilen, des Resultats.
     * @return Integer
     */
    public function rowCount() {
        return $this->_result->rowCount();
    }
    
    /**
     * Enthält genaue Informationen zu einem Error, falls es einen gab.
     * @return Array
     */
    public function getErrorInfo() {
        return $this->_result->err;
    }
    
    public function getQueryString() {
        return $this->_result->queryString;
    }
    
}
?>