<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\MySql;

use Storm\Query\QueryInterface;
use Storm\Query\AbstractQuery;

/**
 * Generates utility query strings
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Utility extends AbstractQuery implements QueryInterface
{
  /**
   * @var ?string $query The query string
   */
  protected $query = null;

  /**
   * Query for dropping a table
   *
   * @param *string $table The name of the table
   *
   * @return QueryInterface
   */
  public function dropTable(string $table): QueryInterface
  {
    $this->query = sprintf('DROP TABLE `%s`', $table);
    return $this;
  }

  /**
   * Returns the string version of the query
   *
   * @return string
   */
  public function getQuery(): string
  {
    return sprintf('%s;', $this->query);
  }

  /**
   * Query for renaming a table
   *
   * @param *string $table The name of the table
   * @param *string $name  The new name of the table
   *
   * @return QueryInterface
   */
  public function renameTable(string $table, string $name): QueryInterface
  {
    $this->query = sprintf('RENAME TABLE `%s` TO `%s`', $table, $name);
    return $this;
  }

  /**
   * Query for showing all columns of a table
   *
   * @param *string $table The name of the table
   * @param ?string $where Filter/s
   *
   * @return QueryInterface
   */
  public function showColumns(string $table, ?string $where = null): QueryInterface
  {
    if ($where) {
      $where = sprintf('WHERE %s', $where);
    }

    $this->query = sprintf('SHOW FULL COLUMNS FROM `%s` %s', $table, $where);
    return $this;
  }

  /**
   * Query for showing all tables
   *
   * @param ?string $like The like pattern
   *
   * @return QueryInterface
   */
  public function showTables(?string $like = null): QueryInterface
  {
    if ($like) {
      $like = sprintf('LIKE %s', $like);
    }

    $this->query = sprintf('SHOW TABLES %s', $like);
    return $this;
  }

  /**
   * Query for truncating a table
   *
   * @param *string $table The name of the table
   *
   * @return QueryInterface
   */
  public function truncate(string $table): QueryInterface
  {
    $this->query = sprintf('TRUNCATE `%s`', $table);
    return $this;
  }
}
