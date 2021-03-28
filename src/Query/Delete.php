<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query;

/**
 * Generates delete query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Delete extends AbstractQuery implements QueryInterface
{
  /**
   * @var array|null $joins List of relatoinal joins
   */
  protected $joins = [];

  /**
   * @var array $where List of filters
   */
  protected $where = [];

  /**
   * Construct: Set the table, if any
   *
   * @param ?string $table The initial name of the table
   */
  public function __construct(?string $table = null)
  {
    if (is_string($table)) {
      $this->setTable($table);
    }
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
    if (!empty($this->joins)) {
      $where = sprintf(' WHERE %s', implode(' AND ', $this->where));
    }

    $query = sprintf('DELETE FROM %s%s%s;', $this->table, $joins, $where);

    return str_replace('  ', ' ', $query);
  }

  /**
   * Allows you to add joins of different types
   * to the query
   *
   * @param *string $type  Join type
   * @param *string $table Table name to join
   * @param *string $where Filter/s
   * @param bool    $using Whether to use "using" syntax (as opposed to "on")
   *
   * @return QueryInterface
   */
  public function join(
    string $type,
    string $table,
    string $where,
    bool $using = true
  ): QueryInterface {
    $linkage = sprintf('ON (%s)', $where);
    if ($using) {
      $linkage = sprintf('USING (%s)', $where);
    }

    $this->joins[] = sprintf('%s JOIN %s %s', $type, $table, $linkage);
    return $this;
  }

  /**
   * Where clause
   *
   * @param array|string $where The where clause
   *
   * @return QueryInterface
   */
  public function where($where): QueryInterface
  {
    if (is_string($where)) {
      $where = [$where];
    }

    $this->where = array_merge($this->where, $where);

    return $this;
  }
}
