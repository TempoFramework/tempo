<?php

namespace Tempo;

use PDO;
use PDOException;
use InvalidArgumentException;
use Tempo\HttpRequest;

class DBConnection
{
    private $connection;
    private $table;
    private $bindings = [];
    private $whereClause = '';
    private $joinClause = ''; 
    private $entity;

    public function __construct($table, $entity)
    {
        $this->entity = new $entity();
        $this->initConnection();
        $this->table = $table;
    }

    private function initConnection()
    {
        $host = 'localhost';
        $database = 'plataformabeautyartist';
        $port = '3306'; 
        $username = 'root';
        $password = 'root';

        try {
            $this->connection = new PDO(
                "mysql:host=$host;port=$port;dbname=$database",
                $username,
                $password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error: " . "Error con conexión a la base de datos: ";
        }
    }

    public static function set($table, $entity)
    {
        return new self($table, $entity);
    }

    public function join($entityClass, array $on, string $type = 'LEFT')
    {
        $tableToJoin = $entityClass::$tabla;

        $fkColumn = key($on);
        $pkColumn = $on[$fkColumn];

        $fk = $this->table . '.' . $fkColumn;
        $pk = $tableToJoin . '.' . $pkColumn;

        $onClause = "$pk = $fk";

        $this->joinClause .= " $type JOIN $tableToJoin ON $onClause";

        return $this;
    }

    public function where($column, $value, $operator = '=')
    {
        $this->addCondition('WHERE', $column, $operator, $value);
        return $this;
    }

    public function and($column, $value, $operator = '=')
    {
        if (empty($value) && $value !== '0' && $value !== 0 && $value == null) {
            return $this;
        }

        if (empty($this->whereClause)) {
            $this->addCondition('WHERE', $column, $operator, $value);
            return $this;
        }
        
        $this->addCondition('AND', $column, $operator, $value);
        return $this;
    }

    public function or($column, $value, $operator = '=')
    {
        if (empty($value) && $value !== '0' && $value !== 0 && $value == null) {
            return $this;
        }

        if (empty($this->whereClause)) {
            $this->addCondition('WHERE', $column, $operator, $value);
            return $this;
        }
        
        $this->addCondition('OR', $column, $operator, $value);
        return $this;
    }

    public function delete()
    {
        if (empty($this->whereClause)) {
            throw new InvalidArgumentException("Error en consulta DELETE para tabla {$this->table}");
        }

        $query = "DELETE FROM {$this->table} {$this->whereClause}";
        $stmt = $this->connection->prepare($query);
        $stmt->execute($this->bindings);

        $this->resetState();

        return $stmt->rowCount();
    }

    public function insert($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException("Los datos para insertar deben ser un array o un objeto.");
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $stmt = $this->connection->prepare($query);
        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }

        $stmt->execute();
        $this->resetState();

        return $this->connection->lastInsertId();
    }

    public function update($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException("Los datos para actualizar deben ser un array o un objeto.");
        }

        if (empty($this->whereClause)) {
            throw new InvalidArgumentException("Error en consulta UPDATE para tabla {$this->table}");
        }

        $setClause = '';
        foreach ($data as $column => $value) {
            if ($value === '' || $value === null) {
                continue; 
            }

            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            $placeholder = $this->createPlaceholder($column);
            $setClause .= "$column = $placeholder, ";
            $this->bindings[$placeholder] = $value;
        }

        if (empty($setClause)) {
            throw new InvalidArgumentException("No hay datos válidos para actualizar en la tabla {$this->table}");
        }

        $setClause = rtrim($setClause, ', ');
        $query = "UPDATE {$this->table} SET $setClause {$this->whereClause}";
        $stmt = $this->connection->prepare($query);
        $stmt->execute($this->bindings);

        $this->resetState();

        return $stmt->rowCount();
    }

    private function addCondition($type, $column, $operator, $value)
    {
        if (empty($this->whereClause)) {
            $this->whereClause = " WHERE ";
        } else {
            $this->whereClause .= " $type ";
        }

        if (strtoupper($operator) == 'LIKE') {
            $value = "%$value%";
        }

        if (strtoupper($value) == 'NOT_NULL') {
            $this->whereClause .= "$column IS NOT NULL";
        } elseif (strtoupper($value) == 'NULL') {
            $this->whereClause .= "$column IS NULL";
        } else {
            $placeholder = $this->createPlaceholder($column);
            $this->whereClause .= "$column $operator $placeholder";
            $this->bindings[$placeholder] = $value;
        }
    }

    public function toFirst($callback = null)
    {
        $query = "SELECT * FROM {$this->table} {$this->joinClause} {$this->whereClause} LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->execute($this->bindings);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        $this->resetState();

        if ($result) {
            if ($callback) {
                $entity = $this->mapToEntity($result);
                return $callback($entity);
            }
            return $result;
        }

        return null;
    }

    public function toList($callback = null, $limit = 0)
    {
        if($limit > 0) {
            $this->whereClause .= " LIMIT $limit";
        }

        $query = "SELECT * FROM {$this->table} {$this->joinClause} {$this->whereClause}";
        $stmt = $this->connection->prepare($query);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);

        $this->resetState();

        if ($callback) {
            $mappedResults = array_map([$this, 'mapToEntity'], $results);
            return array_map($callback, $mappedResults);
        }

        return $results;

    }

    public function toListPaginated($callback = null)
    {
        $pageIndex = HttpRequest::fromQuery('pageIndex') ?? 1;
        $pageSize = HttpRequest::fromQuery('pageSize') ?? 10;

        $offset = ($pageIndex - 1) * $pageSize;

        $query = "SELECT * FROM {$this->table} {$this->joinClause} {$this->whereClause} LIMIT $pageSize OFFSET $offset";
        $stmt = $this->connection->prepare($query);
        $stmt->execute($this->bindings);
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);

        $countQuery = "SELECT COUNT(*) AS total FROM {$this->table} {$this->joinClause} {$this->whereClause}";
        $countStmt = $this->connection->prepare($countQuery);
        $countStmt->execute($this->bindings);
        $totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $totalPages = ceil($totalItems / $pageSize);

        $paginationResult = [
            'items' => $results,
            'totalItems' => (int)$totalItems,
            'pageIndex' => (int)$pageIndex,
            'pageSize' => (int)$pageSize,
            'totalPages' => (int)$totalPages,
        ];

        $this->resetState();

        if ($callback) {
            $mappedResults = array_map([$this, 'mapToEntity'], $results);
            $paginationResult['items'] = array_map($callback, $mappedResults);
        }

        return $paginationResult;
    }


    private function resetState()
    {
        $this->bindings = [];
        $this->whereClause = '';
        $this->joinClause = ''; 
    }

    private function createPlaceholder($column)
    {
        return ':' . str_replace('.', '_', $column);
    }

    private function mapToEntity($result)
    {
        $entityClass = get_class($this->entity);
        $entity = new $entityClass();

        foreach ($result as $key => $value) {
            if (property_exists($entity, $key)) {
                $entity->$key = $value;
            }
        }

        return $entity;
    }
}
