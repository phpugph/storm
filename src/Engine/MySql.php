<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Engine;

use PDO;

use UGComponents\Helper\InstanceTrait;
use UGComponents\Helper\LoopTrait;
use UGComponents\Helper\ConditionalTrait;

use UGComponents\Profiler\InspectorTrait;
use UGComponents\Profiler\LoggerTrait;

use Storm\Query\QueryInterface;
use Storm\Query\Select as QuerySelect;

use Storm\Query\MySql\Alter;
use Storm\Query\MySql\Create;
use Storm\Query\MySql\Utility;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a MySql database. This class also
 * lays out query building methods that auto renders a
 * valid query the specific database will understand without
 * actually needing to know the query language. Extending
 * all Sql classes, comes coupled with loosely defined
 * searching, collections and models.
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class MySql extends AbstractEngine implements EngineInterface
{
  use InstanceTrait,
    LoopTrait,
    ConditionalTrait,
    InspectorTrait,
    LoggerTrait;

  /**
   * @var string $host Database host
   */
  protected string $host = 'localhost';

  /**
   * @var ?string $name Database name
   */
  protected ?string $name = null;

  /**
   * @var ?string $user Database user name
   */
  protected ?string $user = null;

  /**
   * @var ?string $pass Database password
   */
  protected ?string $pass = null;

  /**
   * @var ?string $port Database port
   */
  protected ?string $port = null;

  /**
   * Construct: Store connection information
   *
   * @param *string $host Database host
   * @param ?string $name Database name
   * @param ?string $user Database user name
   * @param ?string $pass Database password
   * @param ?number $port Database port
   */
  public function __construct(
    string $host,
    ?string $name = null,
    ?string $user = null,
    ?string $pass = null,
    ?int $port = null
  ) {
    $this->host = $host;
    $this->name = $name;
    $this->user = $user;
    $this->pass = $pass;
    $this->port = $port;
  }

  /**
   * Connects to the database
   *
   * @param PDO|array $options the connection options
   *
   * @return EngineInterface
   */
  public function connect($options = []): EngineInterface
  {
    if ($options instanceof PDO) {
      $this->connection = $options;
      return $this;
    }

    if (!is_array($options)) {
      $options = array();
    }

    $host = $port = null;

    if (!is_null($this->host)) {
      $host = 'host='.$this->host.';';
      if (!is_null($this->port)) {
        $port = 'port='.$this->port.';';
      }
    }

    $connection = 'mysql:'.$host.$port.'dbname='.$this->name;

    $this->connection = new PDO($connection, $this->user, $this->pass, $options);

    return $this;
  }

  /**
   * Returns the alter query builder
   *
   * @param ?string $table The table name
   *
   * @return QueryInterface
   */
  public function getAlterQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(Alter::class, $table);
  }

  /**
   * Returns the columns and attributes given the table name
   *
   * @param *string        $table   The name of the table
   * @param ?array|string  $filters Where filters
   *
   * @return array
   */
  public function getColumns(string $table, $filters = null): array
  {
    $query = $this->getUtilityQuery();

    if (is_array($filters)) {
      foreach ($filters as $i => $filter) {
        //array('post_id=%s AND post_title IN %s', 123, array('asd'));
        $format = array_shift($filter);
        $filter = $this->bind($filter);
        $filters[$i] = vsprintf($format, $filter);
      }
    }

    $query->showColumns($table, $filters);
    return $this->query($query, $this->getBinds());
  }

  /**
   * Returns the create query builder
   *
   * @param ?string $table The table name
   *
   * @return QueryInterface
   */
  public function getCreateQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(Create::class, $table);
  }

  /**
   * Peturns the primary key name given the table
   *
   * @param *string $table Table name
   *
   * @return string
   */
  public function getPrimaryKey(string $table): string
  {
    $query = $this->getUtilityQuery();
    $results = $this->getColumns($table, "`Key` = 'PRI'");
    return isset($results[0]['Field']) ? $results[0]['Field'] : null;
  }

  /**
   * Returns a listing of tables in the DB
   *
   * @param ?string $like The like pattern
   *
   * @return array
   */
  public function getTables(?string $like = null): array
  {
    $query = $this->getUtilityQuery();
    $like = $like ? $this->bind($like) : null;
    $results = $this->query($query->showTables($like), $this->getBinds());

    $newResults = [];
    foreach ($results as $result) {
      foreach ($result as $key => $value) {
        $newResults[] = $value;
        break;
      }
    }

    return $newResults;
  }

  /**
   * Returns the whole enitre schema and rows
   * of the current table
   *
   * @param *string $table Name of table
   *
   * @return string
   */
  public function getTableSchema(string $table): string
  {
    $backup = [];
    //get the schema
    $schema = $this->getColumns($table);
    if (count($schema)) {
      //lets rebuild this schema
      $query = $this->getCreateQuery()->setTable($table);

      foreach ($schema as $field) {
        //first try to parse what we can from each field
        $fieldTypeArray = explode(' ', $field['Type']);
        $typeArray = explode('(', $fieldTypeArray[0]);

        $type = $typeArray[0];

        $length = null;
        if (isset($typeArray[1])) {
          $length = str_replace(')', '', $typeArray[1]);
        }

        $attribute = isset($fieldTypeArray[1]) ? $fieldTypeArray[1] : null;

        $null = strtolower($field['Null']) == 'no' ? false : true;

        $increment = strtolower($field['Extra']) == 'auto_increment' ? true : false;

        //lets now add a field to our schema class
        $query->addField($field['Field'], [
          'type'        => $type,
          'length'      => $length,
          'attribute'     => $attribute,
          'null'        => $null,
          'default'       => $field['Default'],
          'auto_increment'  => $increment
        ]);

        //set keys where found
        switch ($field['Key']) {
          case 'PRI':
            $query->addPrimaryKey($field['Field']);
            break;
          case 'UNI':
            $query->addUniqueKey($field['Field'], [$field['Field']]);
            break;
          case 'MUL':
            $query->addKey($field['Field'], [$field['Field']]);
            break;
        }
      }

      //store the query but dont run it
      $backup[] = $query;
    }

    //get the rows
    $rows = $this->query($this->getSelectQuery()->from($table)->getQuery());

    if (count($rows)) {
      //lets build an insert query
      $query = $this->getInsertQuery($table);

      foreach ($rows as $index => $row) {
        foreach ($row as $key => $value) {
          $query->set($key, $value, $index);
        }
      }

      //store the query but dont run it
      $backup[] = $query->getQuery(true);
    }

    return implode("\n\n", $backup);
  }

  /**
   * Returns the alter query builder
   *
   * @return QueryInterface
   */
  public function getUtilityQuery(): QueryInterface
  {
    return $this->resolve(Utility::class);
  }

  /**
   * Removes data that is not a column of the table
   *
   * @param *string $table        The name of the table
   * @param *array  $data         The raw data to clean
   * @param bool    $withPrimary  whether to include primary keys
   *
   * @return array
   */
  public function getValidData(
    string $table,
    array $data,
    bool $withPrimary = false
  ): array {
    $columns = $this->getColumns($table);

    $valid = [];
    foreach ($columns as $i => $column) {
      $name = $column['Field'];
      if (!$withPrimary && $column['Key'] === 'PRI') {
        continue;
      }

      if (!isset($data[$name])) {
        continue;
      }

      $valid[$name] = $data[$name];
    }

    return $valid;
  }
}
