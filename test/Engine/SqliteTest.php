<?php

namespace Storm\Engine;

use PDO;
use StdClass;
use PHPUnit\Framework\TestCase;

use Storm\SqlFactory;

use UGComponents\Profiler\InspectorHandler;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class SqliteTest extends TestCase
{
  /**
   * @var Sqlite
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $connection = include(dirname(__DIR__).'/assets/sqlite.php');
    $this->object = SqlFactory::load($connection);
    $connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Engine\Sqlite::connect
   */
  public function testConnect()
  {
    $instance = $this->object->connect(include(dirname(__DIR__).'/assets/sqlite.php'));

    $this->assertInstanceOf('Storm\Engine\Sqlite', $instance);
  }

  /**
   * @covers Storm\Engine\Sqlite::getAlterQuery
   */
  public function testGetAlterQuery()
  {
    $instance = $this->object->getAlterQuery('foobar');
    $this->assertInstanceOf('Storm\Query\Sqlite\Alter', $instance);
  }

  /**
   * @covers Storm\Engine\Sqlite::getColumns
   */
  public function testGetColumns()
  {
    $actual = $this->object->getColumns('unit_post');
    $this->assertTrue(is_array($actual));
  }

  /**
   * @covers Storm\Engine\Sqlite::getCreateQuery
   */
  public function testGetCreateQuery()
  {
    $instance = $this->object->getCreateQuery('foobar');
    $this->assertInstanceOf('Storm\Query\Sqlite\Create', $instance);
  }

  /**
   * @covers Storm\Engine\Sqlite::getPrimaryKey
   */
  public function testGetPrimaryKey()
  {
    $actual = $this->object->getPrimaryKey('unit_post');
    $this->assertEquals('post_id', $actual);
  }

  /**
   * @covers Storm\Engine\Sqlite::getTables
   */
  public function testGetTables()
  {
    $actual = $this->object->getTables();
    $this->assertEquals('unit_post', $actual[0]['name']);
  }

  /**
   * @covers Storm\Engine\Sqlite::insertRows
   */
  public function testInsertRows()
  {
    $instance = $this->object->insertRows('unit_post', array(
      array(
        'post_slug'      => 'unit-test-2-'.md5(uniqid()),
        'post_title'     => 'Unit Test 2',
        'post_detail'     => 'Unit Test Detail 2',
        'post_published'   => date('Y-m-d'),
        'post_created'     => date('Y-m-d H:i:s'),
        'post_updated'     => date('Y-m-d H:i:s')),
      array(
        'post_slug'      => 'unit-test-3-'.md5(uniqid()),
        'post_title'     => 'Unit Test 3',
        'post_detail'     => 'Unit Test Detail 3',
        'post_published'   => date('Y-m-d'),
        'post_created'     => date('Y-m-d H:i:s'),
        'post_updated'     => date('Y-m-d H:i:s'))
    ));

    $this->assertInstanceOf('Storm\Engine\Sqlite', $instance);
  }

  /**
   * @covers Storm\Engine\Sqlite::getSelectQuery
   */
  public function testGetSelectQuery()
  {
    $instance = $this->object->getSelectQuery();
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Engine\Sqlite::getUtilityQuery
   */
  public function testGetUtilityQuery()
  {
    $instance = $this->object->getUtilityQuery();
    $this->assertInstanceOf('Storm\Query\Sqlite\Utility', $instance);
  }
}
