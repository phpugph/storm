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

use Storm\Query\Sqlite\Alter;
use Storm\Query\Sqlite\Create;
use Storm\Query\Sqlite\Utility;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a Sqlite database. This class also
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
class Sqlite extends AbstractEngine implements EngineInterface
{
  use InstanceTrait,
    LoopTrait,
    ConditionalTrait,
    InspectorTrait,
    LoggerTrait;

  /**
   * @var string $path Sqlite file path
   */
  protected $path = null;

  /**
   * Construct: Store connection information
   *
   * @param *string $path Sqlite file path
   */
  public function __construct($path)
  {
    $this->path = $path;
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

    $this->connection = new PDO('sqlite:'.$this->path);
    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
   * @param *string $table The name of the table
   *
   * @return array
   */
  public function getColumns(string $table): array
  {
    $query = $this->getUtilityQuery()->showColumns($table);

    $results = $this->query($query, $this->getBinds());

    $columns = [];
    foreach ($results as $column) {
      $key = null;
      if ($column['pk'] == 1) {
        $key = 'PRI';
      }

      $columns[] = [
        'Field'   => $column['name'],
        'Type'    => $column['type'],
        'Default'   => $column['dflt_value'],
        'Null'    => $column['notnull'] != 1,
        'Key'     => $key
      ];
    }

    return $columns;
  }

  /**
   * Returns the create query builder
   *
   * @param *string $name Name of table
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
   * @param *string $table The table name
   *
   * @return string
   */
  public function getPrimaryKey(string $table): string
  {
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
    return $this->query($query->showTables($like), $this->getBinds());
  }

  /**
   * Inserts multiple rows into a table
   *
   * @param *string    $table   Table name
   * @param *array     $setting Key/value 2D array matching table columns
   * @param bool|array $bind    Whether to compute with binded variables
   *
   * @return EngineInterface
   */
  public function insertRows(
    string $table,
    array $settings,
    $bind = true
  ): EngineInterface {
    //this is an array of arrays
    foreach ($settings as $index => $setting) {
      //Sqlite no available multi insert
      //there's work arounds, but no performance gain
      $this->insertRow($table, $setting, $bind);
    }

    return $this;
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
    if ($select === '*') {
      $select = 'ROWID,*';
    }

    return parent::getSelectQuery($select);
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
}
