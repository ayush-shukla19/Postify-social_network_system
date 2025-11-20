<?php
require_once __DIR__ . "/../config/config.php";

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $port = DB_PORT;   
    public $conn;

    public function __construct() {
        try {
            // Add $this->port as 5th argument
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->port);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}
?>
