<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\PostGreSql;

use Storm\Query\Delete as QueryDelete;

/**
 * Generates delete query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Delete extends QueryDelete
{
  /**
   * @var array $table Table name
   */
  protected $table = null;

  /**
   * @var array $where List of filters
   */
  protected $where = [];

  /**
   * Construct: set table name, if given
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
    return sprintf(
      'DELETE FROM "%s" WHERE %s;',
      $this->table,
      implode(' AND ', $this->where)
    );
  }
}
