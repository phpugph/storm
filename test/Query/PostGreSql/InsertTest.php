<?php

namespace Storm\Query\PostGreSql;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-30 at 04:38:38.
 */
class InsertTest extends TestCase
{
  /**
   * @var QueryInsert
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Insert;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Query\PostGreSql\Insert::getQuery
   */
  public function testGetQuery()
  {
    $actual = $this->object->getQuery();
		$this->assertEquals('INSERT INTO "" ("") VALUES ;', $actual);
  }

  /**
   * @covers Storm\Query\PostGreSql\Insert::set
   */
  public function testSet()
  {
    $instance = $this->object->set('foo', 'bar');
		$this->assertInstanceOf('Storm\Query\PostGreSql\Insert', $instance);
  }
}
