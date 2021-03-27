<?php

namespace Storm\Query;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class Storm_Query_Insert_Test extends TestCase
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
    $this->object = new Insert('foobar');
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Query\Insert::getQuery
   */
  public function testGetQuery()
  {
    $actual = $this->object->getQuery();
		$this->assertEquals('INSERT INTO foobar () VALUES ;', $actual);
  }

  /**
   * @covers Storm\Query\Insert::set
   */
  public function testSet()
  {
    $instance = $this->object->set('foo', 'bar');
		$this->assertInstanceOf('Storm\Query\Insert', $instance);
  }

  /**
   * @covers Storm\Query\Insert::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foobar');
		$this->assertInstanceOf('Storm\Query\Insert', $instance);
  }
}