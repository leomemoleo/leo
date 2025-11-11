<?php
/**
 * Base Model Class
 * MINALIA Parfüm E-Ticaret Platformu
 */

class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find record by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find record by column
     */
    public function findBy($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    /**
     * Get all records
     */
    public function all($orderBy = null, $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get records with pagination
     */
    public function paginate($page = 1, $perPage = 10, $conditions = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;

        // Build WHERE clause
        $where = '';
        $params = [];
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $where = ' WHERE ' . implode(' AND ', $whereParts);
        }

        // Build ORDER BY clause
        $order = '';
        if ($orderBy) {
            $order = " ORDER BY {$orderBy}";
        }

        // Get total count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table}{$where}");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();

        // Get records
        $sql = "SELECT * FROM {$this->table}{$where}{$order} LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        return [
            'data' => $records,
            'pagination' => paginate($total, $perPage, $page)
        ];
    }

    /**
     * Insert record
     */
    public function insert($data) {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($values)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        $setParts = [];
        $values = [];

        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) .
               " WHERE {$this->primaryKey} = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Delete record
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        $where = '';
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $where = ' WHERE ' . implode(' AND ', $whereParts);
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table}{$where}");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Execute raw query
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollBack();
    }

    /**
     * Check if record exists
     */
    public function exists($column, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = ?";
        $params = [$value];

        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
