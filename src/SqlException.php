<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm;

use Exception;

/**
 * SQL exceptions
 *
 * @package  Storm
 * @category Sql
 * @standard PSR-2
 */
class SqlException extends Exception
{
  /**
   * @const string QUERY_ERROR Error template
   */
  const QUERY_ERROR = '%s Query: %s';

  /**
   * @const string TABLE_NOT_SET Error template
   */
  const TABLE_NOT_SET = 'No default table set or was passed.';

  /**
   * @const string DATABASE_NOT_SET Error template
   */
  const DATABASE_NOT_SET = 'No default database set or was passed.';

  /**
   * @const string UNKNOWN_PDO Error template
   */
  const UNKNOWN_PDO = 'Could not match an SQL handler with %s';

  /**
   * Create a new exception for query errors
   *
   * @param *string $query
   * @param *string $error
   *
   * @return SqlException
   */
  public static function forQueryError($query, $error)
  {
    return new static(sprintf(static::QUERY_ERROR, $query, $error));
  }

  /**
   * Create a new exception for table not set
   *
   * @return SqlException
   */
  public static function forTableNotSet()
  {
    return new static(static::TABLE_NOT_SET);
  }

  /**
   * Create a new exception for database not set
   *
   * @return SqlException
   */
  public static function forDatabaseNotSet()
  {
    return new static(static::DATABASE_NOT_SET);
  }

  /**
   * Create a new exception for unknown PDO
   *
   * @param *string $name
   *
   * @return SqlException
   */
  public static function forUnknownPDO($name)
  {
    return new static(sprintf(static::UNKNOWN_PDO, $name));
  }
}
