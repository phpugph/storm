<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm;

use PDO;

use Storm\Engine\MySql;
use Storm\Engine\PostGreSql;
use Storm\Engine\Sqlite;

/**
 * Auto loads up the right handler
 *
 * @package  Storm
 * @category Sql
 * @standard PSR-2
 */
class SqlFactory
{
  /**
   * Auto loads up the right handler given the PDO connection or config info
   *
   * @param *PDO|array $connection
   *
   * @return MySql|PostGreSql|Sqlite
   */
  public static function load(PDO|array $connection): MySql|PostGreSql|Sqlite
  {
    if (is_array($connection)) {
      return static::loadConfig($connection);
    }

    return static::loadPDO($connection);
  }

  /**
   * Auto loads up the right handler given the PDO connection
   *
   * @param *PDO $connection
   *
   * @return MySql|PostGreSql|Sqlite
   */
  public static function loadPDO(PDO $connection): MySql|PostGreSql|Sqlite
  {
    $name = $connection->getAttribute(PDO::ATTR_DRIVER_NAME);

    switch ($name) {
      case 'mysql':
        return MySql::loadPDO($connection);
      case 'pgsql':
        return PostGreSql::loadPDO($connection);
      case 'sqlite':
        return Sqlite::loadPDO($connection);
      default:
        throw SqlException::forUnknownPDO($name);
    }
  }

  /**
   * Auto loads up the right handler given the connection info
   *
   * @param *array $config
   *
   * @return MySql|PostGreSql|Sqlite
   */
  public static function loadConfig(array $config): MySql|PostGreSql|Sqlite
  {
    //make sure all possible keys are defined
    foreach (['type', 'name', 'user', 'pass', 'path'] as $key) {
      if (!isset($config[$key])) {
        $config[$key] = null;
      }
    }

    switch ($config['type']) {
      case 'mysql':
        if (!isset($config['port'])) {
          $config['port'] = 3306;
        }

        return new Mysql(
          $config['host'],
          $config['name'],
          $config['user'],
          $config['pass'],
          $config['port']
        );
      case 'pgsql':
        if (!isset($config['port'])) {
          $config['port'] = 5432;
        }

        return new PostGreSql(
          $config['host'],
          $config['name'],
          $config['user'],
          $config['pass'],
          $config['port']
        );
      case 'sqlite':
        return new Sqlite($config['path']);
      default:
        throw SqlException::forMisconfiguration();
    }
  }
}
