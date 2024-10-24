<?php

class Database {
    private $pdo;

    // Constructor - automatically connects to the database
    public function __construct() {
        // Load environment variables (assuming environment variables are set in Vercel)
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $port = $_ENV['DB_PORT'] ?? 5432;  // Default PostgreSQL port is 5432

        // Set up the DSN (Data Source Name) for PostgreSQL
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";

        try {
            // Establish a new PDO connection
            $this->pdo = new PDO($dsn);
            // Set error mode to exception for better error handling
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle any connection errors
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Method to execute SELECT queries and return results
    public function select($query, $params = []) {
        try {
            // Prepare the query
            $stmt = $this->pdo->prepare($query);
            // Execute with parameters (if provided)
            $stmt->execute($params);
            // Fetch all the results as an associative array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle query errors
            throw new Exception("Select query failed: " . $e->getMessage());
        }
    }

    // Method to execute INSERT, UPDATE, DELETE queries
    public function execute($query, $params = []) {
        try {
            // Prepare the query
            $stmt = $this->pdo->prepare($query);
            // Execute with parameters
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // Handle query errors
            throw new Exception("Query execution failed: " . $e->getMessage());
        }
    }

    // Method to get the last inserted ID (useful for INSERT queries)
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    // Close the PDO connection (optional, PHP usually handles this)
    public function close() {
        $this->pdo = null;
    }
}
