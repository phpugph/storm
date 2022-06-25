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

use Storm\Query\PostGreSql\Delete;
use Storm\Query\PostGreSql\Insert;
use Storm\Query\PostGreSql\Select;
use Storm\Query\PostGreSql\Update;
use Storm\Query\PostGreSql\Alter;
use Storm\Query\PostGreSql\Create;
use Storm\Query\PostGreSql\Utility;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a PostGreSql database. This class also
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
class PostGreSql extends AbstractEngine implements EngineInterface
{
  use InstanceTrait,
    LoopTrait,
    ConditionalTrait,
    InspectorTrait,
    LoggerTrait;

  /**
   * @var string $host Database host
   */
  protected $host = 'localhost';

  /**
   * @var string $port Database port
   */
  protected $port = '5432';

  /**
   * @var ?string $name Database name
   */
  protected $name = null;

  /**
   * @var ?string $user Database user name
   */
  protected $user = null;

  /**
   * @var ?string $pass Database password
   */
  protected $pass = null;

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

    $connection = 'pgsql:'.$host.$port.'dbname='.$this->name;

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
   * Query for showing all columns of a table
   *
   * @param *string $table  The name of the table
   * @param ?string $schema Name of schema
   *
   * @return array
   */
  public function getColumns(string $table, ?string $schema = null): array
  {
    $select = [
      'columns.table_schema',
      'columns.column_name',
      'columns.ordinal_position',
      'columns.column_default',
      'columns.is_nullable',
      'columns.data_type',
      'columns.character_maximum_length',
      'columns.character_octet_length',
      'pg_class2.relname AS index_type'
    ];

    $where = [
      "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
      'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
      'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
      'pg_class2.oid = pg_index.indexrelid'
    ];

    if ($schema) {
      $where[1] .= ' AND columns.table_schema="'.$schema.'"';
    }

    $query = $this
      ->getSelectQuery($select)
      ->from('pg_attribute')
      ->join('inner', 'pg_class AS pg_class1', $where[0], false)
      ->join('inner', 'information_schema.COLUMNS  AS columns', $where[1], false)
      ->join('left', 'pg_index', $where[2], false)
      ->join('left', 'pg_class AS pg_class2', $where[3], false)
      ->getQuery();

    $results = $this->query($query);

    $columns = [];
    foreach ($results as $column) {
      $key = null;
      if (strpos($column['index_type'] ?? '', '_pkey') !== false) {
        $key = 'PRI';
      } else if (strpos($column['index_type'] ?? '', '_key') !== false) {
        $key = 'UNI';
      }

      $columns[] = [
        'Field'   => $column['column_name'],
        'Type'    => $column['data_type'],
        'Default'   => $column['column_default'],
        'Null'    => $column['is_nullable'],
        'Key'     => $key
      ];
    }

    return $columns;
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
   * Returns the delete query builder
   *
   * @param ?string $table The table name
   *
   * @return QueryInterface
   */
  public function getDeleteQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(Delete::class, $table);
  }

  /**
   * Query for showing all columns of a table
   *
   * @param *string $table  the name of the table
   * @param ?string $schema if from a particular schema
   *
   * @return array
   */
  public function getIndexes(string $table, ?string $schema = null): array
  {
    $select = [
      'columns.column_name',
      'pg_class2.relname AS index_type'
    ];

    $where = [
      "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
      'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
      'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
      'pg_class2.oid = pg_index.indexrelid'
    ];

    if ($schema) {
      $where[1] .= ' AND columns.table_schema="'.$schema.'"';
    }

    $query = $this
      ->getSelectQuery($select)
      ->from('pg_attribute')
      ->join('inner', 'pg_class AS pg_class1', $where[0], false)
      ->join('inner', 'information_schema.COLUMNS  AS columns', $where[1], false)
      ->join('inner', 'pg_index', $where[2], false)
      ->join('inner', 'pg_class AS pg_class2', $where[3], false)
      ->getQuery();

    return $this->query($query);
  }

  /**
   * Returns the insert query builder
   *
   * @param ?string $table The table name
   *
   * @return QueryInterface
   */
  public function getInsertQuery(?string $table = null): QueryInterface
  {
    return $this->resolve(Insert::class, $table);
  }

  /**
   * Query for showing all columns of a table
   *
   * @param *string $table  the name of the table
   * @param ?string $schema if from a particular schema
   *
   * @return array
   */
  public function getPrimary(string $table, ?string $schema = null)
  {
    $select = ['columns.column_name'];

    $where = [
      "pg_attribute.attrelid = pg_class1.oid AND pg_class1.relname='".$table."'",
      'columns.column_name = pg_attribute.attname AND columns.table_name=pg_class1.relname',
      'pg_class1.oid = pg_index.indrelid AND pg_attribute.attnum = ANY(pg_index.indkey)',
      'pg_class2.oid = pg_index.indexrelid'];

    if ($schema) {
      $where[1] .= ' AND columns.table_schema="'.$schema.'"';
    }

    $query = $this
      ->getSelectQuery($select)
      ->from('pg_attribute')
      ->join('inner', 'pg_class AS pg_class1', $where[0], false)
      ->join('inner', 'information_schema.COLUMNS  AS columns', $where[1], false)
      ->join('inner', 'pg_index', $where[2], false)
      ->join('inner', 'pg_class AS pg_class2', $where[3], false)
      ->where('pg_class2.relname LIKE \'%_pkey\'')
      ->getQuery();

    return $this->query($query);
  }

  /**
   * Returns the select query builder
   *
   * @param *string|array $select Column list
   *
   * @return QueryInterface
   */
  public function getSelectQuery($select = '*'): QueryInterface
  {
    return $this->resolve(Select::class, $select);
  }

  /**
   * Returns a listing of tables in the DB
   *
   * @return array
   */
  public function getTables(): array
  {
    $query = $this
      ->getSelectQuery('tablename')
      ->from('pg_tables')
      ->where("tablename NOT LIKE 'pg\\_%'")
      ->where("tablename NOT LIKE 'sql\\_%'")
      ->getQuery();

    return $this->query($query);
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
    return $this->resolve(Update::class, $table);
  }

  /**
   * Set schema search paths
   *
   * @param *string $schema Schema name
   *
   * @return EngineInterface
   */
  public function setSchema(string $schema): EngineInterface
  {
    $schema = func_get_args();

    $schema = "'".implode("','", $schema)."'";

    $query = $this->getUtilityQuery()->setSchema($schema);
    $this->query($query);

    return $this;
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
