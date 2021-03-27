<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Mapper;

use Storm\Engine\EngineInterface;
use Storm\Query\QueryInterface;
use Storm\SqlException;

use UGComponents\Resolver\ResolverException;

/**
 * Sql Search
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Search extends Remove
{
  /**
   * @const string ASC Sort direction
   */
  const ASC   = 'ASC';

  /**
   * @const string DESC Sort direction
   */
  const DESC  = 'DESC';

  /**
   * @var EngineInterface $database = null Database object
   */
  protected $database = null;

  /**
   * @var array $columns List of columns
   */
  protected $columns = [];

  /**
   * @var array $sort List of orders and directions
   */
  protected $sort = [];

  /**
   * @var array $group List of "group bys"
   */
  protected $group = [];

  /**
   * @var array $having List of "havings"
   */
  protected $having = [];

  /**
   * @var array $start Pagination start
   */
  protected $start = 0;

  /**
   * @var array $range Pagination range
   */
  protected $range = 0;

  /**
   * Construct: Store database
   *
   * @param *EngineInterface $database Database object
   */
  public function __construct(EngineInterface $database)
  {
    $this->database = $database;
  }

  /**
   * Magical processing of sortBy
   * and filterBy Methods
   *
   * @param *string $name Name of method
   * @param *array  $args Arguments to pass
   *
   * @return mixed
   */
  public function __call(string $name, array $args)
  {
    //if method starts with filterBy
    if (strpos($name, 'filterBy') === 0) {
      //ex. filterByUserName('Chris', '-')
      //choose separator
      $separator = '_';
      if (isset($args[1]) && is_scalar($args[1])) {
        $separator = (string) $args[1];
      }

      //transform method to column name
      $key = substr($name, 8);
      $key = preg_replace("/([A-Z0-9])/", $separator."$1", $key);
      $key = substr($key, strlen($separator));
      $key = strtolower($key);

      //if arg isn't set
      if (!isset($args[0])) {
        //default is null
        $args[0] = null;
      }

      //generate key
      if (is_array($args[0])) {
        $key = $key.' IN %s';
      } else {
        $key = $key.'=%s';
      }

      //add it to the search filter
      $this->addFilter($key, $args[0]);

      return $this;
    }

    //if method starts with sortBy
    if (strpos($name, 'sortBy') === 0) {
      //ex. sortByUserName('Chris', '-')
      //determine separator
      $separator = '_';
      if (isset($args[1]) && is_scalar($args[1])) {
        $separator = (string) $args[1];
      }

      //transform method to column name
      $key = substr($name, 6);
      $key = preg_replace("/([A-Z0-9])/", $separator."$1", $key);
      $key = substr($key, strlen($separator));
      $key = strtolower($key);

      //if arg isn't set
      if (!isset($args[0])) {
        //default is null
        $args[0] = null;
      }

      //add it to the search sort
      $this->addSort($key, $args[0]);

      return $this;
    }

    try {
      return $this->__callResolver($name, $args);
    } catch (ResolverException $e) {
      throw new SqlException($e->getMessage());
    }
  }

  /**
   * Adds sort
   *
   * @param *string $column Column name
   * @param string  $order  ASC or DESC
   *
   * @return MapperInterface
   */
  public function addSort(string $column, ?string $order = self::ASC): MapperInterface
  {
    if ($order != self::DESC) {
      $order = self::ASC;
    }

    $this->sort[$column] = $order;

    return $this;
  }

  /**
   * Alias to setTable
   *
   * @param *string $table
   *
   * @return MapperInterface
   */
  public function from(string $table): MapperInterface
  {
    return $this->setTable($table);
  }

  /**
   * Returns the results in a collection
   *
   * @return Collection
   */
  public function getCollection(): Collection
  {
    $collection = $this
      ->resolve(Collection::class)
      ->setDatabase($this->database);

    if ($this->table) {
      $collection->setTable($this->table);
    }

    return $collection->set($this->getRows());
  }

  /**
   * Returns the one result in a model
   *
   * @param int $index Row index to return
   *
   * @return Model
   */
  public function getModel(int $index = 0): Model
  {
    return $this->getCollection()->offsetGet($index);
  }

  /**
   * Builds query based on the data given
   *
   * @return QueryInterface
   */
  public function getQuery(): QueryInterface
  {
    $query = $this->database->getSelectQuery();

    if ($this->table) {
      $query->from($this->table);
    }

    foreach ($this->joins as $join) {
      $where = $join['where'];
      if (!empty($join['binds'])) {
        foreach ($join['binds'] as $i => $value) {
          $join['binds'][$i] = $this->database->bind($value);
        }

        $where = vsprintf($where, $join['binds']);
      }

      $query->join($join['type'], $join['table'], $where, $join['using']);
    }

    foreach ($this->filters as $i => $filter) {
      //array('post_id=%s AND post_title IN %s', 123, array('asd'));
      $where = $filter['where'];
      if (!empty($filter['binds'])) {
        foreach ($filter['binds'] as $i => $value) {
          $filter['binds'][$i] = $this->database->bind($value);
        }

        $where = vsprintf($where, $filter['binds']);
      }

      $query->where($where);
    }

    return $query;
  }

  /**
   * Returns the one result
   *
   * @param ?int    $index  Row index to return
   * @param ?string $column Specific column to return
   *
   * @return ?array
   */
  public function getRow(?int $index = 0, ?string $column = null): ?array
  {
    $rows = $this->getRows();

    if (!is_null($column) && isset($rows[$index][$column])) {
      return $rows[$index][$column];
    } else if (is_null($column) && isset($rows[$index])) {
      return $rows[$index];
    }

    return null;
  }

  /**
   * Returns the array rows
   *
   * @param ?callable $fetch
   *
   * @return array|MapperInterface
   */
  public function getRows(?callable $fetch = null)
  {
    return $this->query($fetch);
  }

  /**
   * Returns the total results
   *
   * @return int
   */
  public function getTotal(): int
  {
    $query = $this->getQuery()->select('COUNT(*) as total');

    $rows = $this->database->query($query, $this->database->getBinds());

    if (!isset($rows[0]['total'])) {
      return 0;
    }

    return $rows[0]['total'];
  }

  /**
   * Group by clause
   *
   * @param string $group Column name
   *
   * @return MapperInterface
   */
  public function groupBy(string $group): MapperInterface
  {
    if (is_string($group)) {
      $group = [$group];
    }

    $this->group = $group;
    return $this;
  }

  /**
   * Having clause
   *
   * @param string $having Column name
   *
   * @return MapperInterface
   */
  public function having(string $having): MapperInterface
  {
    if (is_string($having)) {
      $having = [$having];
    }

    $this->having = $having;
    return $this;
  }

  /**
   * Queries the database
   *
   * @param ?callable $fetch Whether to fetch all the rows
   *
   * @return array|MapperInterface
   */
  public function query(?callable $fetch = null)
  {
    $query = $this->getQuery();

    if (!empty($this->columns)) {
      $query->select(implode(', ', $this->columns));
    }

    foreach ($this->sort as $key => $value) {
      $query->sortBy($key, $value);
    }

    if ($this->range) {
      $query->limit($this->start, $this->range);
    }

    if (!empty($this->group)) {
      $query->groupBy($this->group);
    }

    if (!empty($this->having)) {
      $query->having($this->having);
    }

    $rows = $this->database->query($query, $this->database->getBinds(), $fetch);

    if (!$fetch) {
      return $rows;
    }

    return $this;
  }

  /**
   * Sets Columns
   *
   * @param string[,string..]|array $columns List of table columns
   *
   * @return MapperInterface
   */
  public function setColumns($columns): MapperInterface
  {
    if (!is_array($columns)) {
      $columns = func_get_args();
    }

    $this->columns = $columns;

    return $this;
  }

  /**
   * Sets the pagination page
   *
   * @param int $page Pagination page
   *
   * @return MapperInterface
   */
  public function setPage(int $page): MapperInterface
  {
    if ($page < 1) {
      $page = 1;
    }

    if ($this->range == 0) {
      $this->setRange(25);
    }

    $this->start = ($page - 1) * $this->range;

    return $this;
  }

  /**
   * Sets the pagination range
   *
   * @param int $range Pagination range
   *
   * @return MapperInterface
   */
  public function setRange(int $range): MapperInterface
  {
    if ($range < 0) {
      $range = 25;
    }

    $this->range = $range;

    return $this;
  }

  /**
   * Sets the pagination start
   *
   * @param int $start Pagination start
   *
   * @return MapperInterface
   */
  public function setStart(int $start): MapperInterface
  {
    if ($start < 0) {
      $start = 0;
    }

    $this->start = $start;

    return $this;
  }
}
