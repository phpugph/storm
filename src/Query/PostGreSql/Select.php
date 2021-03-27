<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\PostGreSql;

use Storm\Query\Select as QuerySelect;

/**
 * Generates select query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Select extends QuerySelect
{
  /**
   * Returns the string version of the query
   *
   * @return string
   */
  public function getQuery(): string
  {
    $joins = '';
    if (!empty($this->joins)) {
      $joins = implode(' ', $this->joins);
    }

    $where = '';
    if (!empty($this->joins)) {
      $where = sprintf('WHERE %s', implode(' AND ', $this->where));
    }

    $sort = '';
    if (!empty($this->sortBy)) {
      $sort = sprintf('ORDER BY %s', implode(', ', $this->sortBy));
    }

    $limit = '';
    if (!is_null($this->page) && $this->length) {
      $limit = sprintf('LIMIT %s OFFSET %s', $this->length, $this->page);
    }

    $group = '';
    if (!empty($this->group)) {
      $group = sprintf('GROUP BY %s', implode(', ', $this->group));
    }

    $having = '';
    if (!empty($this->having)) {
      $having = sprintf('HAVING %s', implode(', ', $this->having));
    }

    $query = sprintf(
      'SELECT %s FROM %s %s %s %s %s %s %s;',
      $this->select,
      $this->table,
      $joins,
      $where,
      $group,
      $having,
      $sort,
      $limit
    );

    return str_replace('  ', ' ', $query);
  }
}
