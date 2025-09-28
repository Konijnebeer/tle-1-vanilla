<?php

require_once 'env.php';

/**
 * Enhanced Database Class with Clear, Unique Methods
 * 
 * This class provides a singleton pattern database connection with specialized methods
 * that have clear, unique purposes. Each method is designed for specific use cases:
 * 
 * DATA RETRIEVAL:
 * - getRow()       : Get single record as associative array
 * - getRows()      : Get multiple records as array of associative arrays
 * - getValue()     : Get single value (useful for COUNT, MAX, etc.)
 * - getById()      : Get record by ID (convenience method)
 * 
 * DATA MODIFICATION:
 * - createRecord() : Insert new record, returns auto-generated ID
 * - updateRecords(): Update records, returns affected row count
 * - deleteRecords(): Delete records, returns affected row count
 * 
 * UTILITIES:
 * - recordExists() : Check if record exists, returns boolean
 * - countRecords() : Count records in table with optional WHERE clause
 * - executeQuery() : Raw SQL execution for complex queries
 * 
 * TRANSACTIONS:
 * - beginTransaction(), commit(), rollback()
 * 
 * LEGACY SUPPORT:
 * - Old methods (fetch, fetchAll, insert, update, delete, query) are kept for
 *   backward compatibility but marked as deprecated
 * 
 * @author Enhanced Database Class
 * @version 2.0
 */

class Database
{
    private $connection;
    private static $instance = null;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

            if (ENVIRONMENT === 'development') {
                // echo "Database connection established successfully.\n";
            }
        } catch (PDOException $e) {
            if (ENVIRONMENT === 'development') {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please try again later.");
            }
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Execute a raw SQL query and return the prepared statement
     * Use this for complex queries that don't fit other methods
     */
    public function executeQuery($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (ENVIRONMENT === 'development') {
                throw new Exception("Query failed: " . $e->getMessage());
            } else {
                throw new Exception("Query failed. Please try again later.");
            }
        }
    }

    /**
     * Get a single row from database
     * Returns associative array or false if no row found
     */
    public function getRow($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Get multiple rows from database
     * Returns array of associative arrays
     */
    public function getRows($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get a single value from database (first column of first row)
     * Useful for COUNT(), MAX(), etc.
     */
    public function getValue($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Create a new record and return the auto-generated ID
     * Returns the last inserted ID or 0 if no auto-increment
     */
    public function createRecord($sql, $params = [])
    {
        $this->executeQuery($sql, $params);
        return $this->connection->lastInsertId();
    }

    /**
     * Update existing records and return number of affected rows
     * Returns integer count of updated rows
     */
    public function updateRecords($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete records and return number of affected rows
     * Returns integer count of deleted rows
     */
    public function deleteRecords($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Check if a record exists
     * Returns boolean true/false
     */
    public function recordExists($sql, $params = [])
    {
        $stmt = $this->executeQuery($sql, $params);
        return $stmt->fetch() !== false;
    }

    /**
     * Count records matching criteria
     * Returns integer count
     */
    public function countRecords($table, $whereClause = '', $params = [])
    {
        $sql = "SELECT COUNT(*) FROM `{$table}`";
        if (!empty($whereClause)) {
            $sql .= " WHERE {$whereClause}";
        }
        return (int) $this->getValue($sql, $params);
    }

    /**
     * Get a record by ID (assumes 'id' column)
     * Returns associative array or false if not found
     */
    public function getById($table, $id, $columns = '*')
    {
        $sql = "SELECT {$columns} FROM `{$table}` WHERE id = ? LIMIT 1";
        return $this->getRow($sql, [$id]);
    }

    /**
     * Begin a database transaction
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit the current transaction
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Rollback the current transaction
     */
    public function rollback()
    {
        return $this->connection->rollBack();
    }

    // Legacy methods for backward compatibility (deprecated)
    // TODO: Update existing code to use new methods and remove these

    /**
     * @deprecated Use getRow() instead
     */
    public function fetch($sql, $params = [])
    {
        return $this->getRow($sql, $params);
    }

    /**
     * @deprecated Use getRows() instead
     */
    public function fetchAll($sql, $params = [])
    {
        return $this->getRows($sql, $params);
    }

    /**
     * @deprecated Use createRecord() instead
     */
    public function insert($sql, $params = [])
    {
        return $this->createRecord($sql, $params);
    }

    /**
     * @deprecated Use updateRecords() instead
     */
    public function update($sql, $params = [])
    {
        return $this->updateRecords($sql, $params);
    }

    /**
     * @deprecated Use deleteRecords() instead
     */
    public function delete($sql, $params = [])
    {
        return $this->deleteRecords($sql, $params);
    }

    /**
     * @deprecated Use executeQuery() instead
     */
    public function query($sql, $params = [])
    {
        return $this->executeQuery($sql, $params);
    }
}
