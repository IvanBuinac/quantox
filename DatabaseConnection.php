<?php


namespace Students;


class DatabaseConnection
{
    var $host="localhost";
    var $username="root";
    var $password="";
    var $database="students";
    public $dbc;

    function __construct() {
        $this->dbc = mysqli_connect($this->host, $this->username, $this->password,           $this->database) or die('Error connecting to DB');
    }


    function query($query, $type = 'assoc') {

    
        $result = $this->dbc->query($query);
    
        $rows = array();
            if ($type == 'assoc') {
                while($row = $result->fetch_assoc()) {
                $rows[] = $row;
                }
            } else {    
                while($row = $result->fetch_object()) {
                $rows[] = $row;
                }   
            }
        return $rows;
    
    }

    public function fetch($sql)
    {
        $array = mysqli_fetch_all($this->query($sql));

        return $array;
    }

    public function close()
    {
        return mysqli_close($this->dbc);
    }
}