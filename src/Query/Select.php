<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query;

/**
 * Generates select query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Select extends Delete implements QueryInterface
{
  /**
   * @var ?string $select List of columns
   */
  protected $select = null;

  /**
   * @var array $sortBy List of order and directions
   */
  protected $sortBy = [];

  /**
   * @var array $group List of "group bys"
   */
  protected $group = [];

  /**
   * @var array $having List of "havings"
   */
  protected $having = [];

  /**
   * @var int|null $page Pagination start
   */
  protected $page = null;

  /**
   * @var int|null $length Pagination range
   */
  protected $length = null;

  /**
   * Construct: Set the columns, if any
   *
   * @param string|array $select Column names
   */
  public function __construct($select = '*')
  {
    $this->select($select);
  }

  /**
   * From clause
   *
   * @param *string $from Main table
   *
   * @return QueryInterface
   */
  public function from(string $from): QueryInterface
  {
    $this->table = $from;
    return $this;
  }

  /**
   * Returns the string version of the query
   *
   * @return string
   */
  public function getQuery(): string
  {
    $joins = '';
    if (!empty($this->joins)) {
      $joins = ' ' . implode(' ', $this->joins);
    }

    $where = '';
    if (!empty($this->where)) {
      $where = sprintf(' WHERE %s', implode(' AND ', $this->where));
    }

    $sort = '';
    if (!empty($this->sortBy)) {
      $sort = sprintf(' ORDER BY %s', implode(', ', $this->sortBy));
    }

    $limit = '';
    if (!is_null($this->page) && $this->length) {
      $limit = sprintf(' LIMIT %s, %s', $this->page, $this->length);
    }

    $group = '';
    if (!empty($this->group)) {
      $group = sprintf(' GROUP BY %s', implode(', ', $this->group));
    }

    $having = '';
    if (!empty($this->having)) {
      $having = sprintf(' HAVING %s', implode(', ', $this->having));
    }

    $query = sprintf(
      'SELECT %s FROM %s%s%s%s%s%s%s',
      $this->select,
      $this->table,
      $joins,
      $where,
      $group,
      $having,
      $sort,
      $limit
    );

    return trim($query) . ';';
  }

  /**
   * Group by clause
   *
   * @param *string|array $group List of "group bys"
   *
   * @return QueryInterface
   */
  public function groupBy($group): QueryInterface
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
   * @return QueryInterface
   */
  public function having(string $having): QueryInterface
  {
    if (is_string($having)) {
      $having = [$having];
    }

    $this->having = $having;
    return $this;
  }

  /**
   * Limit clause
   *
   * @param *string|int $page   Pagination start
   * @param *string|int $length Pagination range
   *
   * @return QueryInterface
   */
  public function limit(int $page, int $length): QueryInterface
  {
    $this->page = $page;
    $this->length = $length;

    return $this;
  }

  /**
   * Select clause
   *
   * @param string|array $select Select columns
   *
   * @return QueryInterface
   */
  public function select($select = '*'): QueryInterface
  {
    //if select is an array
    if (is_array($select)) {
      //transform into a string
      $select = implode(', ', $select);
    }

    $this->select = $select;

    return $this;
  }

  /**
   * Order by clause
   *
   * @param *string $field Column name
   * @param string  $order Direction
   *
   * @return QueryInterface
   */
  public function sortBy(string $field, string $order = 'ASC'): QueryInterface
  {
    $this->sortBy[] = sprintf('%s %s', $field, $order);
    return $this;
  }
}
