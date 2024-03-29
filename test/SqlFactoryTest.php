<?php

namespace Storm;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class SqlFactoryTest extends TestCase
{
  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\SqlFactory::load
   * @covers Storm\SqlFactory::loadPDO
   */
  public function testLoad()
  {
    $mysql = SqlFactory::load(include(__DIR__.'/assets/mysql.php'));
    $this->assertInstanceOf('Storm\Engine\MySql', $mysql);

    //$pgsql = SqlFactory::load(include(__DIR__.'/assets/pgsql.php'));
    //$this->assertInstanceOf('Storm\Engine\PostGreSql', $pgsql);

    $sqlite = SqlFactory::load(include(__DIR__.'/assets/sqlite.php'));
    $this->assertInstanceOf('Storm\Engine\Sqlite', $sqlite);
  }
}
