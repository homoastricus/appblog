<?php

namespace App\Core\Database;

use App\Core\App;
use PDO;
use PDOException;
use PDOStatement;
use Exception;

class QueryBuilder
{
    /**
     * This is the PDO instance.
     * @var PDO
     */
    protected $pdo;

    /**
     * This is the class name a Model will be bound to.
     * @var
     */
    protected string $class_name = "";

    /**
     * This is the current SQL query.
     * @var
     */
    protected string $sql;

    protected string $order_column = "";

    protected string $order_direction = "";

    /**
     * This method is the constructor for the QueryBuilder class and simply initializes a new PDO object.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * This method returns the PDO instance.
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * This method returns the last set SQL query.
     *
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * This method sets the class name to bind the Model to.
     * @param mixed $class_name
     * @return QueryBuilder
     */
    public function setClassName(mixed $class_name): QueryBuilder
    {
        $this->class_name = $class_name;
        return $this;
    }

    /**
     * This method selects all of the rows from a table in a database.
     * @param string $table
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectAll(string $table, string $limit = "", string $offset = ""): bool|array
    {
        return $this->select($table, "*", $limit, $offset);
    }

    /**
     * This method selects rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param string $limit
     * @param string $offset
     * @param string $sort_column
     * @param string $sort_dir
     * @return array|false
     * @throws Exception
     */
    public function selectAllWhere(string $table, $where, string $limit = "", string $offset = "",
                                   string $sort_column = "", string $sort_dir = ""): bool|array
    {
        return $this->selectWhere($table, "*", $where, $limit, $offset, $sort_column, $sort_dir);
    }

    /**
     * This method returns the number of rows in a table.
     * @param string $table
     * @return  int|bool
     * @throws Exception
     */
    public function count(string $table): bool|int
    {
        $this->sql = "SELECT COUNT(*) FROM {$table}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method returns the number of rows in a table where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @return int|bool
     * @throws Exception
     */
    public function countWhere(string $table, $where)
    {
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "SELECT COUNT(*) FROM {$table} WHERE {$mapped_wheres}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method selects rows from a table in a database.
     * @param string $table
     * @param string $columns
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function select(string $table, string $columns, string $limit = "", string $offset = ""): bool|array
    {
        $limit = $this->prepareLimit($limit);
        $offset = $this->prepareOffset($offset);
        $sort_sql = "";
        if ($this->order_column !== "" && $this->order_direction !== "") {
            $sort_sql = "order by $this->order_column $this->order_direction";
        }
        $this->sql = "SELECT {$columns} FROM {$table} {$sort_sql} {$limit} {$offset}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * @param string $order_column
     * @param string $order_direction
     * @return void
     */
    public function order(string $order_column, string $order_direction): void
    {
        $this->order_column = $order_column;
        $this->order_direction = $order_direction;
    }

    /**
     * This method selects rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param string $columns
     * @param $where
     * @param string $limit
     * @param string $offset
     * @return array|false
     * @throws Exception
     */
    public function selectWhere(string $table, string $columns, $where, string $limit = "", string $offset = ""): bool|array
    {
        $limit = $this->prepareLimit($limit);
        $offset = $this->prepareOffset($offset);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $sort_sql = "";
        if ($this->order_column !== "" && $this->order_direction !== "") {
            $sort_sql = "order by $this->order_column $this->order_direction";
        }
        $this->sql = "SELECT {$columns} FROM {$table} WHERE {$mapped_wheres} {$sort_sql} {$limit} {$offset}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return false;
    }

    /**
     * This method deletes rows from a table in a database.
     * @param string $table
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function delete(string $table, string $limit = ""): int
    {
        $limit = $this->prepareLimit($limit);
        $this->sql = "DELETE FROM {$table} {$limit}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }


    /**
     * This method deletes rows from a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $where
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function deleteWhere(string $table, $where, string $limit = ""): int
    {
        $limit = $this->prepareLimit($limit);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = "DELETE FROM {$table} WHERE {$mapped_wheres} {$limit}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($where);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method inserts data into a table in a database.
     * @param string $table
     * @param $parameters
     * @return string
     * @throws Exception
     */
    public function insert(string $table, $parameters): string
    {
        $names = $this->prepareCommaSeparatedColumnNames($parameters);
        $values = $this->prepareCommaSeparatedColumnValues($parameters);
        $this->sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            $names,
            $values
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($parameters);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return "";
    }

    /**
     * This method updates data in a table in a database.
     * @param string $table
     * @param $parameters
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function update(string $table, $parameters, string $limit = ""): int
    {
        $limit = $this->prepareLimit($limit);
        $set = $this->prepareNamed($parameters);
        $this->sql = sprintf(
            'UPDATE %s SET %s %s',
            $table,
            $set,
            $limit
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute($parameters);
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method updates data in a table in a database where one or more conditions are matched.
     * @param string $table
     * @param $parameters
     * @param $where
     * @param string $limit
     * @return int
     * @throws Exception
     */
    public function updateWhere(string $table, $parameters, $where, string $limit = ""): int
    {
        $limit = $this->prepareLimit($limit);
        $set = $this->prepareUnnamed($parameters);
        $parameters = $this->prepareParameters($parameters);
        $where = $this->prepareWhere($where);
        $mapped_wheres = $this->prepareMappedWheres($where);
        $where = array_column($where, 3);
        $this->sql = sprintf(
            'UPDATE %s SET %s WHERE %s %s',
            $table,
            $set,
            $mapped_wheres,
            $limit
        );
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute(array_merge($parameters, $where));
            return $statement->rowCount();
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method selects all of the rows from a table in a database.
     * @param string $table
     * @return array|int
     * @throws Exception
     */
    public function describe(string $table): int|array
    {
        $this->sql = "DESCRIBE {$table}";
        try {
            $statement = $this->pdo->prepare($this->sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method executes raw SQL against a database.
     * @param string $sql
     * @param array $parameters
     * @return array|int
     * @throws Exception
     */
    public function raw(string $sql, array $parameters = [])
    {
        try {
            $this->sql = $sql;
            $statement = $this->pdo->prepare($sql);
            $statement->execute($parameters);
            $output = $statement->rowCount();
            if (stripos($sql, "SELECT") === 0) {
                $output = $statement->fetchAll(PDO::FETCH_CLASS, $this->class_name ?: "stdClass");
            }
            return $output;
        } catch (PDOException $e) {
            $this->handlePDOException($e);
        }
        return 0;
    }

    /**
     * This method prepares the where clause array for the query builder.
     * @param $where
     * @return mixed
     */
    private function prepareWhere($where): mixed
    {
        $array = $where;
        foreach ($where as $key => $value) {
            if (count($value) < 4) {
                array_unshift($array[$key], 0);
            }
        }
        return $array;
    }

    /**
     * This method prepares the limit statement for the query builder.
     * @param $limit
     * @return string
     */
    private function prepareLimit($limit): string
    {
        return (!empty($limit) ? " LIMIT " . $limit : "");
    }

    /**
     * This method prepares the offset statement for the query builder.
     * @param $offset
     * @return string
     */
    private function prepareOffset($offset): string
    {
        return (!empty($offset) ? " OFFSET " . $offset : "");
    }

    /**
     * This method prepares the comma separated names for the query builder.
     * @param $parameters
     * @return string
     */
    private function prepareCommaSeparatedColumnNames($parameters): string
    {
        return implode(', ', array_keys($parameters));
    }

    /**
     * This method prepares the comma separated values for the query builder.
     * @param $parameters
     * @return string
     */
    private function prepareCommaSeparatedColumnValues($parameters): string
    {
        return ':' . implode(', :', array_keys($parameters));
    }

    /**
     * This method prepares the mapped wheres.
     * @param $where
     * @return string
     */
    private function prepareMappedWheres($where): string
    {
        $mapped_wheres = '';
        foreach ($where as $clause) {
            $modifier = $mapped_wheres === '' ? '' : " AND ";
            $mapped_wheres .= " {$modifier} {$clause[1]} {$clause[2]} ?";
        }
        return $mapped_wheres;
    }

    /**
     * This method prepares the unnamed columns.
     * @param $parameters
     * @return string
     */
    private function prepareUnnamed($parameters): string
    {
        return implode(', ', array_map(
            static function ($property) {
                return "{$property} = ?";
            },
            array_keys($parameters)
        ));
    }

    /**
     * This method prepares the named columns.
     * @param $parameters
     * @return string
     */
    private function prepareNamed($parameters): string
    {
        return implode(', ', array_map(
            static function ($property) {
                return "{$property} = :{$property}";
            },
            array_keys($parameters)
        ));
    }

    /**
     * This method prepares the parameters with numeric keys.
     * @param $parameters
     * @param int $counter
     * @return mixed
     */
    private function prepareParameters($parameters, int $counter = 1): mixed
    {
        foreach ($array = $parameters as $key => $value) {
            unset($parameters[$key]);
            $parameters[$counter] = $value;
            $counter++;
        }
        return $parameters;
    }

    /**
     * This method binds values from an array to the PDOStatement.
     * @param PDOStatement $PDOStatement
     * @param $array
     * @param int $counter
     */
    private function prepareBindings(PDOStatement $PDOStatement, $array, int $counter = 1): void
    {
        foreach ($array as $key => $value) {
            $PDOStatement->bindParam($counter, $value);
            $counter++;
        }
    }

    /**
     * This method handles PDO exceptions.
     * @param PDOException $e
     * @return mixed
     * @throws Exception
     */
    private function handlePDOException(PDOException $e): mixed
    {
        App::logError('There was a PDO Exception. Details: ' . $e);
        if (App::get('config')['options']['debug']) {
            return view('error', ['error' => $e->getMessage()]);
        }
        return view('error');
    }
}