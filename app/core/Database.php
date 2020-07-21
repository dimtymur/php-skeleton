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
        $cols_str = ""; $cols_temp = "";
        foreach ($attrs as $col => $val) {
        	$cols_str .= ",$col"; $cols_temp .= ",?";
        	$vals[] = $val;
        }
        $cols_str   = trim($cols_str, ",");
        $cols_temp  = trim($cols_temp, ",");

        $query = "INSERT INTO $table ($cols_str) VALUES ($cols_temp);";
        $this->query($query, $vals);
    }

    public function records_delete(string $table, string $id_col, $id_val) : void {
        $query = "DELETE FROM $table WHERE $id_col = ?;";
        $this->query($query, [$id_val]);
    }

    public function records_edit(string $table, string $id_col, $id_val, array $attrs) : void {
        $cols_temp = "";
        foreach ($attrs as $col => $val) {
        	$cols_temp .= ",$col=?"; $vals[] = $val;
        }
        $vals[]     = $id_val;
        $cols_temp  = trim($cols_temp, ",");

        $query = "UPDATE $table SET $cols_temp WHERE $id_col = ?;";
        $this->query($query, $vals);
    }

    public function records_get(string $table, string $id_col, $id_val) : array {
        $query = "SELECT * FROM $table WHERE $id_col = ?;";
        return $this->query($query, [$id_val]);
    }
}
