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
abstract class AbstractQuery
{
  /**
   * @var string $table most queries deal with tables
   */
  protected $table = null;

  /**
   * Transform class to string using getQuery
   *
   * @return string
   */
  public function __toString(): string
  {
    return $this->getQuery();
  }

  /**
   * Returns the string version of the query
   *
   * @return string
   */
  abstract public function getQuery(): string;

  /**
   * Set the table name in which you want to delete from
   *
   * @param string $table The table name
   *
   * @return QueryInterface
   */
  public function setTable(string $table): QueryInterface
  {
    $this->table = $table;
    return $this;
  }
}
