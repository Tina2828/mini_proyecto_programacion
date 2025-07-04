<?php
require_once __DIR__.'/DatabaseConnection.php';

class DatabaseQueryBuilder {
    protected $connection;
    protected $table;
    protected $select = [];
    protected $where = [];
    protected $orderBy = [];
    protected $join = [];
    protected $limit = null;
    protected $offset = null;

    public function __construct(DatabaseConnection $connection){
        $this->connection = $connection;
    }

    public function table(string $table): self {
        $this->table = $table;
        return $this;
    }

    public function join(string $table, string $on, string $type = 'INNER'): self
    {
        $this->join[] = [
            'table' => $table,
            'on' => $on,
            'type' => strtoupper($type)
        ];
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->select = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->where[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => $direction
        ];
        return $this;
    }


    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $query = $this->buildQuery();
        $pdo = $this->connection->getConnection();
        $statement = $pdo->prepare($query['sql']);
        $this->bindParams($statement, 'where', $this->bindWhere());
        $statement->execute();
        Log::info("\033[34m{$statement->queryString}\033[0m");
        return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
    }

    public function first(): ?stdClass
    {
        $this->limit(1);
        $results = $this->get();
        return !empty($results) ? $results[0] : null;
    }

    public function count(): int
    {
        $this->select(['COUNT(*) as count']);
        $result = $this->first();
        return $result ? (int) $result->count : 0;
    }


    public function update(array $data): stdClass|null
    {
        if (empty($this->where)) {
            throw new Exception("Update requires a WHERE clause.");
        }

        $setClauses = [];
        foreach ($data as $column => $value) {
            $setClauses[] = "`{$column}` = :update_{$column}";
        }
        $setClause = implode(', ', $setClauses);

        $query = "UPDATE {$this->table} SET {$setClause} " . $this->buildWhere();
        $statement = $this->connection->getConnection()->prepare($query);

        $this->bindParams($statement, 'where', $this->bindWhere());
        $this->bindParams($statement, 'update', $data);

        $statement->execute();

        Log::info("\033[33m{$statement->queryString}\033[0m");
        return $this->first();
    }


    public function delete(): int
    {
        if (empty($this->where)) {
            throw new Exception("Delete requires a WHERE clause.");
        }

        $query = "DELETE FROM {$this->table} " . $this->buildWhere();
        $statement = $this->connection->getConnection()->prepare($query);

        $this->bindParams($statement, 'where', $this->bindWhere());

        $statement->execute();
        Log::info("\033[31m{$statement->queryString}\033[0m");
        return $statement->rowCount() || 0;
    }

    public function insert(array $data): stdClass
    {
        if (empty($data)) {
            throw new Exception("Insert requires data.");
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($col) => ":insert_{$col}", array_keys($data)));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $statement = $this->connection->getConnection()->prepare($query);

        $this->bindParams($statement, "insert", $data);

        $statement->execute();
        Log::info("\033[32m{$statement->queryString}\033[0m");
        $id = $this->connection->getConnection()->lastInsertId();

        $builder = new self($this->connection);
        $builder->table($this->table);
        $builder->where('id', '=', $id);
        return $builder->first();
    }

    public function buildWhere(){
        $params = [];

        if (empty($this->where)) {
          return "";
        }

        $whereClauses = [];
        foreach ($this->where as $i => $condition) {
          $param = ":where_{$i}";
          $whereClauses[] = "{$condition['column']} {$condition['operator']} {$param}";
          $params[$param] = $condition['value'];
        }


        return " WHERE " . implode(' AND ', $whereClauses);
    }

    public function buildOrderBy()
    {
        $orderClauses = [];
        foreach ($this->orderBy as $order) {
            $orderClauses[] = "{$order['column']} {$order['direction']}";
        }
        return " ORDER BY " . implode(', ', $orderClauses);
    }

    public function buildLimit()
    {
        $limitClause = '';
        if ($this->limit !== null) {
            $limitClause = " LIMIT {$this->limit}";
        }
        return $limitClause;
    }

    public function buildJoin()
    {
        $joinClauses = [];
        foreach ($this->join as $join) {
            $joinClauses[] = "{$join['type']} JOIN {$join['table']} ON {$join['on']}";
        }
        return implode(' ', $joinClauses);
    }

    public function buildOffset()
    {
        $offsetClause = '';
        if ($this->offset !== null) {
            $offsetClause = " OFFSET {$this->offset}";
        }
        return $offsetClause;
    }

    protected function bindWhere(): array
    {
        $params = [];

        foreach ($this->where as $i => $condition) {
            $param = "{$i}";
            $params[$param] = $condition['value'];
        }

        return $params;
    }

    protected function buildQuery(): array
    {
        $params = [];

        // SELECT
        $select = empty($this->select) ? '*' : implode(', ', $this->select);
        $sql = "SELECT {$select} FROM {$this->table}";

        // JOIN
        if (!empty($this->join)) {
            $join = $this->buildJoin();
            $sql .= " {$join}";
        }

        // WHERE
        if(!empty($this->where)) {
            $where = $this->buildWhere();
            $sql = "{$sql} {$where}";
        }

        // ORDER BY
        if (!empty($this->orderBy)) {
            $sql .= $this->buildOrderBy();
        }

        // LIMIT
        if( $this->limit !== null) {
            $sql .= $this->buildLimit();
        }

        return ['sql' => $sql, 'params' => $params];
    }

    public function bindParams(PDOStatement &$statement, $prefix = "", $params = []): void {
      foreach ($params as $key => $value) {
        $paramKey = ":{$prefix}_{$key}";

        if ($value instanceof DateTime) {
            $formattedDate = $value->format('Y-m-d H:i:s');
            $statement->bindValue($paramKey, $formattedDate, PDO::PARAM_STR);
            continue;
        }

        if (is_int($value)) {
            $statement->bindValue($paramKey, $value, PDO::PARAM_INT);
            continue;
        }

        if (is_bool($value)) {
            $statement->bindValue($paramKey, $value, PDO::PARAM_BOOL);
            continue;
        }

        if (is_null($value)) {
            $statement->bindValue($paramKey, null, PDO::PARAM_NULL);
            continue;
        }

        $statement->bindValue($paramKey, $value, PDO::PARAM_STR);
    }
  }
}

