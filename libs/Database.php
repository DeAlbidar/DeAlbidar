<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Database extends PDO{

    function __construct($DB_TYPE, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS) {
        parent::__construct($DB_TYPE.':host='.$DB_HOST.';dbname='.$DB_NAME.';charset=utf8', $DB_USER, $DB_PASS);
        //parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTIONS);
        
    }
    
    /**
    * insert
    * @param string $table A name of table to insert into
    * @param string $data An associative array
    */
    public function insert($table, $data){
        ksort($data);

        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));

        $sth = $this->prepare("INSERT INTO $table (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
                $sth->bindValue(":$key", $value);
        }
        $sth->execute();
        if ($sth->rowCount() > 0) {
            $_SESSION['success'] = "Inserted Successfully.";
            return $sth->rowCount();
        }else{
            $_SESSION['error'] = "Something Went Wrong.";
        }
    }
	
    /**
     * update
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     * @param string $where the WHERE query part
     */
    public function update($table, $data, $where){
        ksort($data);

        $fieldDetails = NULL;
        foreach($data as $key=> $value) {
                $fieldDetails .= "`$key`=:$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $sth = $this->prepare("UPDATE $table SET $fieldDetails WHERE $where");

        foreach ($data as $key => $value) {
                $sth->bindValue(":$key", $value);
        }
        $sth->execute();
        if ($sth->rowCount() > 0) {
            $_SESSION['success'] = "Updated Successfully.";
            return $sth->rowCount();
        }else{
            $_SESSION['error'] = "Something Went Wrong.";
        }
    }
    
    /**
    * delete
    * 
    * @param string $table
    * @param string $where
    * @param integer $limit
    * @return integer Affected Rows
    */
    public function delete($table, $where, $limit = 1){
        return $this->exec("DELETE FROM $table WHERE $where LIMIT $limit");
    }

    /**
    * select
    * @param string $sql An SQL string
    * @param array $array Paramters to bind
    * @param constant $fetchMode A PDO Fetch mode
    * @return mixed
    */
    public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC){
        $sth = $this->prepare($sql);
        foreach ($array as $key => $value) {
                $sth->bindValue("$key", $value);
        }
        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }
    
    public static function chanel($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC){
        $sth = $this->prepare($sql);
        foreach ($array as $key => $value) {
                $sth->bindValue("$key", $value);
        }
        $sth->execute();
        return $sth->fetchAll($fetchMode);
    }
}