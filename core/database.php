<?php

class Database {

    /**
     * @var PDO
     */
    protected $pdo;

    public function __construct($dsn, $user, $password) {

        $this->connect($dsn, $user, $password);
    }

    protected function connect($dsn, $user, $password) {

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ERRMODE_EXCEPTION, true);
    }

    /**
     * @return PDO
     */
    public function db() {

        return $this->pdo;
    }

    public function insert($table, array $values, $primaryKey = 'id') {

        $keys = array();
        $fields = array();
        $executeValues = array();

        foreach ($values as $key => $val) {

            $fields[] = '`' . $key . '`';
            $keys[] = ':' . $key;
            $executeValues[':' . $key] = $val;
        }

        $fields = implode(', ', $fields);
        $keys = implode(', ' , $keys);

        $stmt = $this->db()->prepare("INSERT INTO $table ({$fields}) VALUES({$keys})");

        if (!$stmt->execute($executeValues)) {
            return FALSE;
        }

        if (!$primaryKey) {
            return NULL;
        }

        return $this->db()->lastInsertId($primaryKey);
    }

    public function update($table, $primaryValue, array $values, $primaryKey = 'id') {

        $fields = array();
        $executeValues = array(':primaryKey' => $primaryValue);

        foreach ($values as $key => $val) {

            $fields[] = '`' . $key . '` = :' . $key;
            $executeValues[':' . $key] = $val;
        }

        $fields = implode(', ', $fields);

        $stmt = $this->db()->prepare("UPDATE $table SET {$fields} WHERE {$primaryKey} = :primaryKey");

        if (!$stmt->execute($executeValues)) {
            return FALSE;
        }

        return $primaryValue;
    }

    public function delete($table, array $condition, $primaryKey = 'id') {

        if (empty($condition)) {
            return FALSE;
        }

        $where = array();
        $params = array();

        foreach ($condition as $key => $val) {
            $where[] = '`' . $key . '` = :' . $key;
            $params[':' . $key] = $val;
        }

        $where = implode(' AND ', $where);

        if ($primaryKey) {

            $stmt = $this->db()->prepare("SELECT {$primaryKey} FROM {$table}
                WHERE 1 = 1 AND {$where}");
            $stmt->execute($params);

            $primaryKeyValue = $stmt->fetchColumn(0);

        } else {

            $primaryKeyValue = NULL;
        }

        $rmStmt = $this->db()->prepare("DELETE FROM {$table} WHERE 1 = 1 AND {$where}");
        $rmStmt->execute($params);

        return $primaryKeyValue;
    }

    public function select($table, $condition = NULL, $cols = NULL, $sort = NULL, $group = NULL, $limit = NULL, $fetchStyle = PDO::FETCH_OBJ) {

        $params = array();
        $where = array();

        if ($condition) {

            foreach ($condition as $key => $val) {
                $where[] = '`' . $key . '` = :' . $key;
                $params[':' . $key] = $val;
            }
        }

        $where = implode(' AND ', $where);

        if ($where) {
            $where = 'AND ' . $where;
        }

        $order = array();

        if ($sort) {

            foreach ($sort as $key => $val) {

                $order[] = "`{$key}` {$val}";
            }
        }

        $order = implode(', ', $order);

        if ($order) {
            $order = "ORDER BY {$order}";
        }

        if ($group) {
            $group = implode(', ', $group);
            $group = "GROUP BY {$group}";
        }

        $limitBy = '';
        $fetchOne = FALSE;

        if ($limit) {

            if (is_array($limit)) {
                $limitBy = "LIMIT {$limit[0]} OFFSET {$limit[1]}";
            } else {

                if ($limit == 1) {
                    $fetchOne = TRUE;
                }

                $limitBy = "LIMIT {$limit}";
            }
        }

        $columns = array();

        if ($cols) {

            foreach ($cols as $alias => $col) {

                if ($col == '*') {

                    $columns[] = $col;
                } else {

                    if (is_numeric($alias)) {
                        $alias = $col;
                    }

                    $columns[] = "{$col} as {$alias}";
                }
            }

        } else {
            $columns = array('*');
        }

        $columns = implode(', ', $columns);

        $sql = "SELECT {$columns} FROM {$table} WHERE 1 = 1 {$where} {$order} {$group} {$limitBy}";
        $stmt = $this->db()->prepare($sql);

        if ($stmt->execute($params)) {

            if ($fetchOne) {
                return $stmt->fetch($fetchStyle);
            }

            return $stmt->fetchAll($fetchStyle);
        }

        return FALSE;
    }
} 