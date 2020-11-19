<?php

namespace App\Core;

class Database {
    private $host      = "localhost";
    private $dbName    = "skeleton";
    private $username  = "username";
    private $password  = "password";

    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() : void {
        try {
            $this->conn = new \PDO(
                "mysql:host=".$this->host.";dbname=".$this->dbName,
                $this->username,
                $this->password
            );
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    public function query(string $query, array $params=[]) {
        $sth = $this->conn->prepare($query);
        $sth->execute($params);

        if (preg_match("/^SELECT\b/i", $query)) return $sth->fetchAll();
    }

    public function record_add(string $table, array $attrs) : void {
        $columns_str = ""; $columns_temp = "";
        foreach ($attrs as $column => $value) {
        	$columns_str .= ",$column"; $columns_temp .= ",?";
        	$values[] = $value;
        }
        $columns_str = trim($columns_str, ",");
        $columns_temp = trim($columns_temp, ",");

        $query = "INSERT INTO $table ($columns_str) VALUES ($columns_temp);";
        $this->query($query, $values);
    }

    public function records_delete(string $table, string $id_column, $id_value) : void {
        $query = "DELETE FROM $table WHERE $id_column = ?;";
        $this->query($query, [$id_value]);
    }

    public function records_edit(string $table, string $id_column, $id_value, array $attrs) : void {
        $columns_temp = "";
        foreach ($attrs as $column => $value) {
        	$columns_temp .= ",$column=?"; $values[] = $value;
        }
        $values[] = $id_value;
        $columns_temp = trim($columns_temp, ",");

        $query = "UPDATE $table SET $columns_temp WHERE $id_column = ?;";
        $this->query($query, $values);
    }

    public function records_get(string $table, string $id_column, $id_value) : array {
        $query = "SELECT * FROM $table WHERE $id_column = ?;";
        return $this->query($query, [$id_value]);
    }
}
