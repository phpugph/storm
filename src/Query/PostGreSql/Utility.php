<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\PostGreSql;

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
    $this->query = sprintf('DROP TABLE "%s"', $table);
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
    $this->query = sprintf('RENAME TABLE "%s" TO "%s"', $table, $name);
    return $this;
  }

  /**
   * Specify the schema
   *
   * @param *string $schema The schema name
   *
   * @return QueryInterface
   */
  public function setSchema(string $schema): QueryInterface
  {
    $this->query = sprintf('SET search_path TO %s', $schema);
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
    $this->query = sprintf('TRUNCATE "%s"', $table);
    return $this;
  }
}
