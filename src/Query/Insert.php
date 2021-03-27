<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query;

/**
 * Generates insert query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Insert extends AbstractQuery implements QueryInterface
{
  /**
   * @var array $setKey List of keys
   */
  protected $setKey = [];

  /**
   * @var array $setVal List of values
   */
  protected $setVal = [];

  /**
   * Set the table, if any
   *
   * @param ?string $table Table name
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
    $multiValList = [];
    foreach ($this->setVal as $val) {
      $multiValList[] = sprintf('(%s)', implode(', ', $val));
    }

    return sprintf(
      'INSERT INTO %s (%s) VALUES %s;',
      $this->table,
      implode(', ', $this->setKey),
      implode(", \n", $multiValList)
    );
  }

  /**
   * Set clause that assigns a given field name to a given value.
   * You can also use this to add multiple rows in one call
   *
   * @param *string $key   The column name
   * @param ?scalar $value The column value
   * @param int     $index For what row is this for?
   *
   * @return QueryInterface
   */
  public function set(string $key, $value, int $index = 0): QueryInterface
  {
    if (!in_array($key, $this->setKey)) {
      $this->setKey[] = $key;
    }

    if (is_null($value)) {
      $value = 'null';
    } else if (is_bool($value)) {
      $value = $value ? 1 : 0;
    }

    $this->setVal[$index][] = $value;
    return $this;
  }
}
