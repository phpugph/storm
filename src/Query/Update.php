<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query;

/**
 * Generates update query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Update extends Delete implements QueryInterface
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
    $joins = '';
    if (!empty($this->joins)) {
      $joins = implode(' ', $this->joins);
    }

    $where = '';
    if (!empty($this->joins)) {
      $where = sprintf('WHERE %s', implode(' AND ', $this->where));
    }

    $set = [];
    foreach ($this->set as $key => $value) {
      $set[] = "{$key} = {$value}";
    }

    $query = sprintf(
      'UPDATE %s %s SET %s %s;',
      $this->table,
      $joins,
      implode(', ', $set),
      $where
    );

    return str_replace('  ', ' ', $query);
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
      $value = 'null';
    } else if (is_bool($value)) {
      $value = $value ? 1 : 0;
    }

    $this->set[$key] = $value;

    return $this;
  }
}
