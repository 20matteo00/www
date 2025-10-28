<?php
class DB
{
    private $pdo;
    private $dbName;
    private $host;
    private $user;
    private $pass;
    private $charset;
    private $transactionLevel = 0;

    public function __construct($dbName, $config = [])
    {
        $this->dbName = $dbName;
        $this->host = $config['host'] ?? 'localhost';
        $this->user = $config['user'] ?? 'root';
        $this->pass = $config['pass'] ?? 'Matteo00';
        $this->charset = $config['charset'] ?? 'utf8mb4';

        $this->connectAndCreateDB();
    }

    private function connectAndCreateDB()
    {
        try {
            // Connessione al server senza DB
            $dsn = "mysql:host={$this->host};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];

            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);

            // Creazione DB se non esiste
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbName}` CHARACTER SET {$this->charset} COLLATE {$this->charset}_general_ci");

            // Connetti al DB specifico
            $this->pdo->exec("USE `{$this->dbName}`");

        } catch (PDOException $e) {
            throw new Exception("Errore connessione DB: " . $e->getMessage());
        }
    }

    /* ===========================
       GESTIONE TRANSAZIONI
       =========================== */

    public function beginTransaction()
    {
        if ($this->transactionLevel === 0) {
            $this->pdo->beginTransaction();
        }
        $this->transactionLevel++;
        return $this;
    }

    public function commit()
    {
        $this->transactionLevel--;
        if ($this->transactionLevel === 0) {
            $this->pdo->commit();
        }
        return $this;
    }

    public function rollback()
    {
        if ($this->transactionLevel > 0) {
            $this->pdo->rollback();
            $this->transactionLevel = 0;
        }
        return $this;
    }

    public function transaction(callable $callback)
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /* ===========================
       METODI TABLE AVANZATI
       =========================== */

    public function createTable($table, $columns, $options = [])
    {
        $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;
        $engine = $options['engine'] ?? 'InnoDB';
        $charset = $options['charset'] ?? $this->charset;

        $sql = "CREATE TABLE IF NOT EXISTS `$table` ($columnsStr) ENGINE=$engine DEFAULT CHARSET=$charset";

        return $this->pdo->exec($sql) !== false;
    }

    public function addColumn($table, $column, $definition, $after = null)
    {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if ($after) {
            $sql .= " AFTER `$after`";
        }
        return $this->pdo->exec($sql) !== false;
    }

    public function dropColumn($table, $column)
    {
        $sql = "ALTER TABLE `$table` DROP COLUMN `$column`";
        return $this->pdo->exec($sql) !== false;
    }

    public function modifyColumn($table, $column, $definition)
    {
        $sql = "ALTER TABLE `$table` MODIFY COLUMN `$column` $definition";
        return $this->pdo->exec($sql) !== false;
    }

    public function renameColumn($table, $oldColumn, $newColumn, $definition)
    {
        $sql = "ALTER TABLE `$table` CHANGE `$oldColumn` `$newColumn` $definition";
        return $this->pdo->exec($sql) !== false;
    }

    public function addIndex($table, $indexName, $columns, $type = 'INDEX')
    {
        $columnsStr = is_array($columns) ? '`' . implode('`, `', $columns) . '`' : "`$columns`";
        $sql = "ALTER TABLE `$table` ADD $type `$indexName` ($columnsStr)";
        return $this->pdo->exec($sql) !== false;
    }

    public function dropIndex($table, $indexName)
    {
        $sql = "ALTER TABLE `$table` DROP INDEX `$indexName`";
        return $this->pdo->exec($sql) !== false;
    }

    /* ===========================
       CRUD AVANZATO
       =========================== */

    public function insert($table, $data, $onDuplicate = null)
    {
        if (empty($data))
            return false;

        $fields = implode('`,`', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));

        $sql = "INSERT INTO `$table` (`$fields`) VALUES ($placeholders)";

        if ($onDuplicate) {
            $sql .= " ON DUPLICATE KEY UPDATE $onDuplicate";
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function insertMultiple($table, $data, $batchSize = 100)
    {
        if (empty($data))
            return false;

        $chunks = array_chunk($data, $batchSize);
        $totalInserted = 0;

        foreach ($chunks as $chunk) {
            $fields = implode('`,`', array_keys($chunk[0]));
            $placeholders = [];
            $params = [];

            foreach ($chunk as $i => $row) {
                $rowPlaceholders = [];
                foreach ($row as $key => $value) {
                    $placeholder = ":{$key}_{$i}";
                    $rowPlaceholders[] = $placeholder;
                    $params[$placeholder] = $value;
                }
                $placeholders[] = '(' . implode(',', $rowPlaceholders) . ')';
            }

            $sql = "INSERT INTO `$table` (`$fields`) VALUES " . implode(',', $placeholders);
            $stmt = $this->pdo->prepare($sql);

            if ($stmt->execute($params)) {
                $totalInserted += count($chunk);
            }
        }

        return $totalInserted;
    }

    public function upsert($table, $data, $updateFields = [])
    {
        $this->insert($table, $data, $this->buildOnDuplicateUpdate($updateFields ?: array_keys($data)));
    }

    private function buildOnDuplicateUpdate($fields)
    {
        return implode(', ', array_map(fn($field) => "`$field` = VALUES(`$field`)", $fields));
    }

    public function update($table, $data, $where = [], $limit = null)
    {
        if (empty($data))
            return false;

        $set = implode(',', array_map(fn($k) => "`$k`=:set_$k", array_keys($data)));
        $sql = "UPDATE `$table` SET $set";

        $params = [];
        foreach ($data as $k => $v) {
            $params["set_$k"] = $v;
        }

        if (!empty($where)) {
            $whereClause = $this->buildWhereClause($where, $params);
            $sql .= " WHERE $whereClause";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($table, $where = [], $limit = null)
    {
        $sql = "DELETE FROM `$table`";
        $params = [];

        if (!empty($where)) {
            $whereClause = $this->buildWhereClause($where, $params);
            $sql .= " WHERE $whereClause";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    function runQuery(string $sql, array $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            // Decide se eseguire fetchAll o restituire rowCount in base al tipo di query
            $sqlType = strtoupper(substr(trim($sql), 0, 6));

            if (in_array($sqlType, ['SELECT', 'SHOW', 'DESCRI'])) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return $stmt->rowCount();
            }
        } catch (PDOException $e) {
            // Gestisci errori
            error_log("Query error: " . $e->getMessage());
            return false;
        }
    }


    /* ===========================
       SELECT AVANZATO
       =========================== */

    public function select($table, $options = [])
    {
        $columns = $options['columns'] ?? ['*'];
        $where = $options['where'] ?? [];
        $joins = $options['joins'] ?? [];
        $groupBy = $options['groupBy'] ?? '';
        $having = $options['having'] ?? [];
        $orderBy = $options['orderBy'] ?? '';
        $limit = $options['limit'] ?? '';
        $offset = $options['offset'] ?? '';

        $cols = is_array($columns) ? implode(',', $columns) : $columns;
        $sql = "SELECT $cols FROM `$table`";

        // JOIN
        if (!empty($joins)) {
            foreach ($joins as $join) {
                $sql .= " {$join['type']} JOIN `{$join['table']}` ON {$join['condition']}";
            }
        }

        $params = [];

        // WHERE
        if (!empty($where)) {
            $whereClause = $this->buildWhereClause($where, $params);
            $sql .= " WHERE $whereClause";
        }

        // GROUP BY
        if ($groupBy) {
            $sql .= " GROUP BY $groupBy";
        }

        // HAVING
        if (!empty($having)) {
            $havingClause = $this->buildWhereClause($having, $params, 'having_');
            $sql .= " HAVING $havingClause";
        }

        // ORDER BY
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        // LIMIT & OFFSET
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find($table, $where = [], $columns = ['*'])
    {
        $result = $this->select($table, [
            'columns' => $columns,
            'where' => $where,
            'limit' => 1
        ]);
        return $result ? $result[0] : null;
    }

    public function exists($table, $where = [])
    {
        return $this->count($table, $where) > 0;
    }

    public function count($table, $where = [])
    {
        $result = $this->select($table, [
            'columns' => ['COUNT(*) as count'],
            'where' => $where
        ]);
        return $result ? (int) $result[0]['count'] : 0;
    }

    /* ===========================
       QUERY BUILDER HELPERS
       =========================== */

    private function buildWhereClause($where, &$params, $prefix = 'where_')
    {
        $conditions = [];

        foreach ($where as $key => $value) {
            if (is_array($value)) {
                // Operatori speciali
                if (isset($value['operator'])) {
                    $operator = strtoupper($value['operator']);
                    $paramKey = $prefix . $key;

                    switch ($operator) {
                        case 'IN':
                        case 'NOT IN':
                            $placeholders = [];
                            foreach ($value['value'] as $i => $v) {
                                $placeholder = "{$paramKey}_{$i}";
                                $placeholders[] = ":$placeholder";
                                $params[$placeholder] = $v;
                            }
                            $conditions[] = "`$key` $operator (" . implode(',', $placeholders) . ")";
                            break;

                        case 'BETWEEN':
                            $params["{$paramKey}_start"] = $value['value'][0];
                            $params["{$paramKey}_end"] = $value['value'][1];
                            $conditions[] = "`$key` BETWEEN :{$paramKey}_start AND :{$paramKey}_end";
                            break;

                        case 'LIKE':
                        case 'NOT LIKE':
                            $params[$paramKey] = $value['value'];
                            $conditions[] = "`$key` $operator :$paramKey";
                            break;

                        case 'IS NULL':
                        case 'IS NOT NULL':
                            $conditions[] = "`$key` $operator";
                            break;

                        default:
                            $params[$paramKey] = $value['value'];
                            $conditions[] = "`$key` $operator :$paramKey";
                    }
                } else {
                    // Array semplice = IN
                    $placeholders = [];
                    foreach ($value as $i => $v) {
                        $placeholder = "{$prefix}{$key}_{$i}";
                        $placeholders[] = ":$placeholder";
                        $params[$placeholder] = $v;
                    }
                    $conditions[] = "`$key` IN (" . implode(',', $placeholders) . ")";
                }
            } else {
                // Condizione semplice
                $paramKey = $prefix . $key;
                $params[$paramKey] = $value;
                $conditions[] = "`$key` = :$paramKey";
            }
        }

        return implode(' AND ', $conditions);
    }

    /* ===========================
       METODI INFORMATIVI
       =========================== */

    public function getTableStructure($table)
    {
        $stmt = $this->pdo->prepare("DESCRIBE `$table`");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getIndexes($table)
    {
        $stmt = $this->pdo->prepare("SHOW INDEXES FROM `$table`");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getForeignKeys($table)
    {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE REFERENCED_TABLE_SCHEMA = :db AND TABLE_NAME = :table";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['db' => $this->dbName, 'table' => $table]);
        return $stmt->fetchAll();
    }

    public function getTableSize($table)
    {
        $sql = "SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                    table_rows
                FROM information_schema.tables 
                WHERE table_schema = :db AND table_name = :table";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['db' => $this->dbName, 'table' => $table]);
        return $stmt->fetch();
    }

    /* ===========================
       BACKUP E UTILITY
       =========================== */

    public function backup($tables = null, $filePath = null)
    {
        $tables = $tables ?: $this->getTables();
        $filePath = $filePath ?: $this->dbName . '_backup_' . date('Y-m-d_H-i-s') . '.sql';

        $sql = "-- Database: {$this->dbName}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // Struttura tabella
            $createTable = $this->pdo->query("SHOW CREATE TABLE `$table`")->fetch();
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $createTable['Create Table'] . ";\n\n";

            // Dati tabella
            $data = $this->select($table);
            if (!empty($data)) {
                $sql .= "INSERT INTO `$table` VALUES\n";
                $values = [];
                foreach ($data as $row) {
                    $values[] = "('" . implode("','", array_map([$this->pdo, 'quote'], $row)) . "')";
                }
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return file_put_contents($filePath, $sql) !== false ? $filePath : false;
    }

    public function optimize($table = null)
    {
        if ($table) {
            return $this->pdo->exec("OPTIMIZE TABLE `$table`") !== false;
        } else {
            $tables = $this->getTables();
            foreach ($tables as $table) {
                $this->pdo->exec("OPTIMIZE TABLE `$table`");
            }
            return true;
        }
    }

    public function analyze($table = null)
    {
        if ($table) {
            return $this->pdo->exec("ANALYZE TABLE `$table`") !== false;
        } else {
            $tables = $this->getTables();
            foreach ($tables as $table) {
                $this->pdo->exec("ANALYZE TABLE `$table`");
            }
            return true;
        }
    }

    /* ===========================
       METODI ORIGINALI MIGLIORATI
       =========================== */

    public function dropTable($table)
    {
        $sql = "DROP TABLE IF EXISTS `$table`";
        return $this->pdo->exec($sql) !== false;
    }

    public function truncateTable($table)
    {
        $sql = "TRUNCATE TABLE `$table`";
        return $this->pdo->exec($sql) !== false;
    }

    public function tableExists($table)
    {
        $stmt = $this->pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->execute(['table' => $table]);
        return $stmt->rowCount() > 0;
    }

    public function getTables()
    {
        $stmt = $this->pdo->query("SHOW TABLES");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function runRaw($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Determina il tipo di query
        $sqlType = strtoupper(substr(trim($sql), 0, 6));

        if (in_array($sqlType, ['SELECT', 'SHOW', 'DESCRI'])) {
            return $stmt->fetchAll();
        } else {
            return $stmt->rowCount();
        }
    }

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}

/* ===========================
   ESEMPI DI UTILIZZO
   =========================== */

/*
// Inizializzazione
$db = new DB('mio_database', [
    'host' => 'localhost',
    'user' => 'root', 
    'pass' => 'password'
]);

// Creazione tabella
$db->createTable('users', [
    'id INT PRIMARY KEY AUTO_INCREMENT',
    'name VARCHAR(100) NOT NULL',
    'email VARCHAR(100) UNIQUE',
    'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
]);

// Insert multipli
$users = [
    ['name' => 'Mario', 'email' => 'mario@email.com'],
    ['name' => 'Luigi', 'email' => 'luigi@email.com']
];
$db->insertMultiple('users', $users);

// Select avanzato
$results = $db->select('users', [
    'columns' => ['id', 'name', 'email'],
    'where' => [
        'name' => ['operator' => 'LIKE', 'value' => '%Mar%'],
        'id' => ['operator' => 'IN', 'value' => [1, 2, 3]]
    ],
    'orderBy' => 'name ASC',
    'limit' => 10
]);

// Transazioni
$db->transaction(function($db) {
    $db->insert('users', ['name' => 'Test']);
    $db->update('users', ['name' => 'Updated'], ['id' => 1]);
    return true; // commit automatico
});

// Backup
$backupFile = $db->backup(['users', 'posts']);
echo "Backup salvato in: $backupFile";
*/
?>