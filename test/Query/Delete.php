<?php

namespace Storm\Query;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class Storm_Query_Delete_Test extends TestCase
{
  /**
   * @var QueryDelete
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp()
  {
    $this->object = new Delete('foobar');
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown()
  {
  }

  /**
   * @covers Storm\Query\Delete::getQuery
   */
  public function testGetQuery()
  {
    $actual = $this->object->getQuery();
		$this->assertEquals('DELETE FROM foobar;', $actual);
  }

  /**
   * @covers Storm\Query\Delete::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foobar');
		$this->assertInstanceOf('Storm\Query\Delete', $instance);
  }

  /**
   * @covers Storm\Query\Delete::where
   */
  public function testWhere()
  {
    $instance = $this->object->where('foo=bar');
		$this->assertInstanceOf('Storm\Query\Delete', $instance);
  }
}
