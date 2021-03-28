<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Engine;

use StdClass;
use Closure;
use PDO;
use ReflectionClass;

use UGComponents\Resolver\StateTrait;

use Storm\SqlException;

use Storm\Mapper\Model;
use Storm\Mapper\Collection;
use Storm\Mapper\Search;
use Storm\Mapper\Remove;
use Storm\Mapper\Update;
use Storm\Mapper\Insert;

use Storm\Query\QueryInterface;
use Storm\Query\Delete as QueryDelete;
use Storm\Query\Insert as QueryInsert;
use Storm\Query\Select as QuerySelect;
use Storm\Query\Update as QueryUpdate;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a database. This class also lays out
 * query building methods that auto renders a valid query
 * the specific database will understand without actually
 * needing to know the query language.
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
abstract class AbstractEngine
{
  use StateTrait
    {
      StateTrait::__callResolver as __call;
  }

  /**
   * @const int INSTANCE Flag that designates multiton when using ::i()
   */
  const INSTANCE = 0;

  /**
   * @const string FIRST The first index in getQueries
   */
  const FIRST = 'first';

  /**
   * @const string LAST The last index in getQueries
   */
  const LAST = 'last';

  /**
   * @var [RESOURCE] $connection PDO resource
   */
  protected $connection = null;

  /**
   * @var array $binds Bound data from the current query
   */
  protected $binds = [];

  /**
   * Connects to the database
   *
   * @param PDO|array $options The connection options
   *
   * @return EngineInterface
   */
  abstract public function connect($options = []): EngineInterface;

  /**
   * Binds a value and returns the bound key
   *
   * @param *string|array|number|null $value What to bind
   *
   * @return string
   */
  public function bind($value): string
  {
    if (is_array($value)) {
      foreach ($value as $i => $item) {
        $value[$i] = $this->bind($item);
      }

      return sprintf('(%s)', implode(',', $value));
    } else if (is_int($value) || ctype_digit($value)) {
      if (strpos($value, '0') !== 0) {
        return $value;
      }
    }

    $name = ':bind'.count($this->binds).'bind';
    $this->binds[$name] = $value;
    return $name;
  }

  /**
   * Returns collection
   *
   * @param array $data Initial collection data
   *
   * @return Collection
   */
  public function collection(array $data = []): Collection
  {
    return $this
      ->resolve(Collection::class)
      ->setDatabase($this)
      ->set($data);
  }

  /**
   * Removes rows that match a filter
   *
   * @param ?string      $table   The table name
   * @param array|string $filters Filters to test against
   *
   * @return EngineInterface
   */
  public function deleteRows(?string $table = null, $filters = null): EngineInterface
  {
    $query = $this->getDeleteQuery($table);

    //array('post_id=%s AND post_title IN %s', 123, array('asd'));
    if (is_array($filters)) {
      //can be array of arrays
      if (is_array($filters[0])) {
        foreach ($filters as $i => $filter) {
          if (is_array($filters)) {
            $format = array_shift($filter);

            //reindex filters
            $filter = array_values($filter);

            //bind filters
            foreach ($filter as $i => $value) {
              $filter[$i] = $this->bind($value);
            }

            //combine
            $query->where(vsprintf($format, $filter));
          }
        }
      } else {
        $format = array_shift($filters);

        //reindex filters
        $filters = array_values($filters);

        //bind filters
        foreach ($filters as $i => $value) {
          $filters[$i] = $this->bind($value);
        }

        //combine
        $query->where(vsprintf($format, $filters));
      }
    } else {
      $query->where($filters);
    }

    //run the query
    $this->query($query, $this->getBinds());

    return $this;
  }

  /**
   * Returns all the bound values of this query
   *
   * @return array
   */
  public function getBinds(): array
  {
    return $this->binds;
  }

  /**
   * Returns the connection object
   * if no connection has been made
   * it will attempt to make it
   *
   * @return resource PDO connection resource
   */
  public function getConnection(): PDO
  {
    if (!$this->connection) {
      $this->connect();
    }

    return $this->connection;
  }

  /**
   * Returns the delete query builder
   *
   * @param ?string $table The table name
   *
   * @return QueryInterface
   */
  public function getDeleteQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(QueryDelete::class, $table);
  }

  /**
   * Returns the insert query builder
   *
   * @param ?string $table Name of table
   *
   * @return QueryInterface
   */
  public function getInsertQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(QueryInsert::class, $table);
  }

  /**
   * Returns the last inserted id
   *
   * @param ?string $column A particular column name
   *
   * @return int the id
   */
  public function getLastInsertedId(?string $column = null): int
  {
    if (is_string($column)) {
      return $this->getConnection()->lastInsertId($column);
    }

    return $this->getConnection()->lastInsertId();
  }

  /**
   * Returns a model given the column name and the value
   *
   * @param *string $table Table name
   * @param *string $name  Column name
   * @param ?scalar $value Column value
   *
   * @return ?Model
   */
  public function getModel(string $table, string $name, $value): ?Model
  {
    //scalar check
    if ($value && !is_scalar($value)) {
      return null;
    }

    //get the row
    $result = $this->getRow($table, $name, $value);

    if (is_null($result)) {
      return null;
    }

    return $this->model()->setTable($table)->set($result);
  }

  /**
   * Returns a 1 row result given the column name and the value
   *
   * @param *string $table Table name
   * @param *string $name  Column name
   * @param ?scalar $value Column value
   *
   * @return ?array
   */
  public function getRow(string $table, string $name, $value): ?array
  {
    //scalar check
    if ($value && !is_scalar($value)) {
      return null;
    }

    //make the query
    $query = $this
      ->getSelectQuery()
      ->from($table)
      ->where(sprintf('%s = %s', $name, $this->bind($value)))
      ->limit(0, 1);

    //get the results
    $results = $this->query($query, $this->getBinds());

    //if we have results
    if (isset($results[0])) {
      //return it
      return $results[0];
    }

    return null;
  }

  /**
   * Returns the select query builder
   *
   * @param string|array $select Column list
   *
   * @return QueryInterface
   */
  public function getSelectQuery($select = '*'): QueryInterface
  {
    return $this->resolve(QuerySelect::class, $select);
  }

  /**
   * Returns the update query builder
   *
   * @param ?string $table Name of table
   *
   * @return QueryInterface
   */
  public function getUpdateQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(QueryUpdate::class, $table);
  }

  /**
   * Returns insert
   *
   * @param ?string $table Table name
   *
   * @return Insert
   */
  public function insert(?string $table = null): Insert
  {
    $update = $this->resolve(Insert::class, $this);

    if ($table) {
      $update->setTable($table);
    }

    return $update;
  }

  /**
   * Inserts data into a table and returns the ID
   *
   * @param *string    $table   Table name
   * @param *array     $setting Key/value array matching table columns
   * @param bool|array $bind    Whether to compute with binded variables
   *
   * @return EngineInterface
   */
  public function insertRow(string $table, array $settings, $bind = true): EngineInterface
  {
    //build insert query
    $query = $this->getInsertQuery($table);

    //foreach settings
    foreach ($settings as $key => $value) {
      //if value is not a vulnerability
      if (is_null($value) || is_bool($value)) {
        //just add it to the query
        $query->set($key, $value);
        continue;
      }

      //if bind is true or is an array and we want to bind it
      if ($bind === true || (is_array($bind) && in_array($key, $bind))) {
        //bind the value
        $value = $this->bind($value);
      }

      //add it to the query
      $query->set($key, $value);
    }

    //run the query
    $this->query($query, $this->getBinds());

    return $this;
  }

  /**
   * Inserts multiple rows into a table
   *
   * @param *string    $table   Table name
   * @param array      $setting Key/value 2D array matching table columns
   * @param bool|array $bind    Whether to compute with binded variables
   *
   * @return AbstractSql
   */
  public function insertRows(string $table, array $settings, $bind = true): EngineInterface
  {
    //build insert query
    $query = $this->getInsertQuery($table);

    //this is an array of arrays
    foreach ($settings as $index => $setting) {
      //for each column
      foreach ($setting as $key => $value) {
        //if value is not a vulnerability
        if (is_null($value) || is_bool($value)) {
          //just add it to the query
          $query->set($key, $value, $index);
          continue;
        }

        //if bind is true or is an array and we want to bind it
        if ($bind === true || (is_array($bind) && in_array($key, $bind))) {
          //bind the value
          $value = $this->bind($value);
        }

        //add it to the query
        $query->set($key, $value, $index);
      }
    }

    //run the query
    $this->query($query, $this->getBinds());

    return $this;
  }

  /**
   * Adaptor used to force a connection to the handler
   *
   * @param *PDO $connection
   *
   * @return EngineInterface
   */
  public static function loadPDO(PDO $connection): EngineInterface
  {
    $reflection = new ReflectionClass(static::class);
    $instance = $reflection->newInstanceWithoutConstructor();
    return $instance->connect($connection);
  }

  /**
   * Returns model
   *
   * @param array $data The initial data to set
   *
   * @return Model
   */
  public function model(array $data = []): Model
  {
    return $this->resolve(Model::class, $data)->setDatabase($this);
  }

  /**
   * Queries the database
   *
   * @param *string|QueryInterface $query The query to ran
   * @param array                  $binds List of binded values
   * @param ?callable              $fetch Whether to fetch all the rows
   *
   * @return array|EngineInterface
   */
  public function query($query, array $binds = [], ?callable $fetch = null)
  {
    $request = new StdClass();

    $request->query = $query;
    $request->binds = $binds;

    $connection = $this->getConnection();
    $query    = (string) $request->query;
    $stmt     = $connection->prepare($query);

    //bind some more values
    foreach ($request->binds as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    //PDO Execute
    if (!$stmt->execute()) {
      $error = $stmt->errorInfo();

      //unpack binds for the report
      foreach ($binds as $key => $value) {
        $query = str_replace($key, "'$value'", $query);
      }

      //throw Exception
      throw SqlException::forQueryError($query, $error[2]);
    }

    //clear binds
    $this->binds = [];

    if (!is_callable($fetch)) {
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

      //log query
      $this->log([
        'query'   => $query,
        'binds'   => $binds,
        'results'   => $results
      ]);

      return $results;
    }

    if ($fetch instanceof Closure) {
      $fetch = $fetch->bindTo($this, get_class($this));
    }

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      if (call_user_func($fetch, $row, $this) === false) {
        break;
      }
    }

    return $this;
  }

  /**
   * Returns update
   *
   * @param ?string $table Table name
   *
   * @return Remove
   */
  public function remove(?string $table = null): Remove
  {
    $remove = $this->resolve(Remove::class, $this);

    if ($table) {
      $remove->setTable($table);
    }

    return $remove;
  }

  /**
   * Returns search
   *
   * @param ?string $table Table name
   *
   * @return Search
   */
  public function search(?string $table = null): Search
  {
    $search = $this->resolve(Search::class, $this)->setColumns('*');

    if ($table) {
      $search->setTable($table);
    }

    return $search;
  }

  /**
   * Sets all the bound values of this query
   *
   * @param *array $binds key/values to bind
   *
   * @return EngineInterface
   */
  public function setBinds(array $binds): EngineInterface
  {
    $this->binds = $binds;
    return $this;
  }

  /**
   * Sets only 1 row given the column name and the value
   *
   * @param *string $table   Table name
   * @param *string $name  Column name
   * @param ?scalar $value   Column value
   * @param *array  $setting Key/value array matching table columns
   *
   * @return EngineInterface
   */
  public function setRow(
    string $table,
    string $name,
    $value,
    array $setting
  ): EngineInterface {
    //first check to see if the row exists
    $row = $this->getRow($table, $name, $value);

    if (!$row) {
      //we need to insert
      $setting[$name] = $value;
      return $this->insertRow($table, $setting);
    }

    //we need to update this row
    return $this->updateRows($table, $setting, [$name.'=%s', $value]);
  }

  /**
   * Sets up a transaction call
   *
   * @param *callable $callback
   *
   * @return EngineInterface
   */
  public function transaction(callable $callback): EngineInterface
  {
    $connection = $this->getConnection();
    $connection->beginTransaction();

    if ($callback instanceof Closure) {
      $callback = $callback->bindTo($this, get_class($this));
    }

    if (call_user_func($callback, $this) === false) {
      $connection->rollBack();
    } else {
      $connection->commit();
    }

    return $this;
  }

  /**
   * Returns update
   *
   * @param ?string $table Table name
   *
   * @return Update
   */
  public function update(?string $table = null): Update
  {
    $update = $this->resolve(Update::class, $this);

    if ($table) {
      $update->setTable($table);
    }

    return $update;
  }

  /**
   * Updates rows that match a filter given the update settings
   *
   * @param *string           $table   Table name
   * @param *array            $setting Key/value array matching table columns
   * @param array|string|null $filters Filters to test against
   * @param bool|array        $bind    Whether to compute with binded variables
   *
   * @return EngineInterface
   */
  public function updateRows(
    string $table,
    array $settings,
    $filters = null,
    $bind = true
  ): EngineInterface {
    //build the query
    $query = $this->getUpdateQuery($table);

    //foreach settings
    foreach ($settings as $key => $value) {
      //if value is not a vulnerability
      if (is_null($value) || is_bool($value)) {
        //just add it to the query
        $query->set($key, $value);
        continue;
      }

      //if bind is true or is an array and we want to bind it
      if ($bind === true || (is_array($bind) && in_array($key, $bind))) {
        //bind the value
        $value = $this->bind($value);
      }

      //add it to the query
      $query->set($key, $value);
    }

    //array('post_id=%s AND post_title IN %s', 123, array('asd'));
    if (is_array($filters)) {
      //can be array of arrays
      if (is_array($filters[0])) {
        foreach ($filters as $i => $filter) {
          if (is_array($filters)) {
            $format = array_shift($filter);

            //reindex filters
            $filter = array_values($filter);

            //bind filters
            foreach ($filter as $i => $value) {
              $filter[$i] = $this->bind($value);
            }

            //combine
            $query->where(vsprintf($format, $filter));
          }
        }
      } else {
        $format = array_shift($filters);

        //reindex filters
        $filters = array_values($filters);

        //bind filters
        foreach ($filters as $i => $value) {
          $filters[$i] = $this->bind($value);
        }

        //combine
        $query->where(vsprintf($format, $filters));
      }
    } else {
      $query->where($filters);
    }

    //run the query
    $this->query($query, $this->getBinds());

    return $this;
  }
}
