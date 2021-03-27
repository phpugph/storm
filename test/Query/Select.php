<?php

namespace Storm\Query;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class Storm_Query_Select_Test extends TestCase
{
  /**
   * @var QuerySelect
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Select;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Query\Select::from
   */
  public function testFrom()
  {
    $instance = $this->object->from('foobar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::getQuery
   */
  public function testGetQuery()
  {
    $actual = $this->object->getQuery();
    $this->assertEquals('SELECT * FROM;', $actual);
  }

  /**
   * @covers Storm\Query\Select::groupBy
   */
  public function testGroupBy()
  {
    $instance = $this->object->groupBy('foobar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::having
   */
  public function testHaving()
  {
    $instance = $this->object->having('foobar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::join
   */
  public function testJoin()
  {
    $instance = $this->object->join('INNER', 'foobar', 'foo=bar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::limit
   */
  public function testLimit()
  {
    $instance = $this->object->limit(0, 1);
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::select
   */
  public function testSelect()
  {
    $instance = $this->object->select('foobar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::sortBy
   */
  public function testSortBy()
  {
    $instance = $this->object->sortBy('foobar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }

  /**
   * @covers Storm\Query\Select::where
   */
  public function testWhere()
  {
    $instance = $this->object->where('foo=bar');
    $this->assertInstanceOf('Storm\Query\Select', $instance);
  }
}
