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
 * Auto loads up the right handler given the PDO connection
 *
 * @package  Storm
 * @category Sql
 * @standard PSR-2
 */
class SqlFactory
{
  public static function load(PDO $connection)
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
}
