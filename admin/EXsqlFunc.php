<?php
require '../backend/databaseconnection.php';

class Select {
    protected $conn;

    function __construct($connection) {
        $this->conn = $connection;
    }

    function selectQuery($tableName) {
        $sql = "SELECT * FROM `$tableName`";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }

    function selectQueryById($tableName, $id) {
        $sql = "SELECT * FROM `$tableName` WHERE id=$id";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }
}

class Delete {
    protected $conn;

    function __construct($connection) {
        $this->conn = $connection;
    }

    function deleteQueryById($tableName, $id) {
        $sql = "DELETE FROM $tableName WHERE id=$id";
        $result = mysqli_query($this->conn, $sql);
        return $result;
    }
}

class Update {
    protected $conn;

    function __construct($connection) {
        $this->conn = $connection;
    }

    function updateQuery($tableName, $data, $id) {
        $updateValues = '';
        foreach ($data as $column => $value) {
            $value = mysqli_real_escape_string($this->conn, $value);
            $updateValues .= "$column = '$value', ";
        }
        $updateValues = rtrim($updateValues, ', ');

        $sql = "UPDATE $tableName SET $updateValues WHERE id = $id";
        $result = mysqli_query($this->conn, $sql);
        
        return $result;
    }
}
?>
