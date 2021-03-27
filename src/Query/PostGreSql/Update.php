<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\PostGreSql;

use Storm\Query\QueryInterface;
use Storm\Query\Delete as QueryDelete;

/**
 * Generates update query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Update extends QueryDelete
{
  /**
   * @var array $set List of key/values
   */
  protected $set = [];

  /**
   * Returns the string version of the query
   *
   * @return string
   */
  public function getQuery(): string
  {
    $set = [];
    foreach ($this->set as $key => $value) {
      $set[] = sprintf('"%s" = %s', $key, $value);
    }

    return sprintf(
      'UPDATE %s SET %s WHERE %s;',
      $this->table,
      implode(', ', $set),
      implode(' AND ', $this->where)
    );
  }

  /**
   * Set clause that assigns a given field name to a given value.
   *
   * @param *string $key   The column name
   * @param ?scalar $value The column value
   *
   * @return QueryInterface
   */
  public function set(string $key, $value): QueryInterface
  {
    if (is_null($value)) {
      $value = 'NULL';
    } else if (is_bool($value)) {
      $value = $value ? 'TRUE' : 'FALSE';
    }

    $this->set[$key] = $value;

    return $this;
  }
}
