<?php

namespace Storm\Query\Sqlite;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:03.
 */
class UtilityTest extends TestCase
{
  /**
   * @var QueryUtility
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Utility;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Query\Sqlite\Utility::dropTable
   */
  public function testDropTable()
  {
    $instance = $this->object->dropTable('foobar');
		$this->assertInstanceOf('Storm\Query\Sqlite\Utility', $instance);
  }

  /**
   * @covers Storm\Query\Sqlite\Utility::getQuery
   */
  public function testGetQuery()
  {
    $actual = $this->object->getQuery();
		$this->assertEquals(';', $actual);
  }

  /**
   * @covers Storm\Query\Sqlite\Utility::renameTable
   */
  public function testRenameTable()
  {
    $instance = $this->object->renameTable('foo', 'bar');
		$this->assertInstanceOf('Storm\Query\Sqlite\Utility', $instance);
  }

  /**
   * @covers Storm\Query\Sqlite\Utility::showColumns
   */
  public function testShowColumns()
  {
    $instance = $this->object->showColumns('foobar');
		$this->assertInstanceOf('Storm\Query\Sqlite\Utility', $instance);
  }

  /**
   * @covers Storm\Query\Sqlite\Utility::showTables
   */
  public function testShowTables()
  {
    $instance = $this->object->showTables();
		$this->assertInstanceOf('Storm\Query\Sqlite\Utility', $instance);
  }

  /**
   * @covers Storm\Query\Sqlite\Utility::truncate
   */
  public function testTruncate()
  {
    $instance = $this->object->truncate('foobar');
		$this->assertInstanceOf('Storm\Query\Sqlite\Utility', $instance);
  }
}
