<?php

namespace Storm\Mapper;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class CollectionTest extends TestCase
{
  /**
   * @var Collection
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Collection;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Mapper\Collection::getModel
   */
  public function testGetModel()
  {
    $instance = $this->object->getModel();
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);
  }

  /**
   * @covers Storm\Mapper\Collection::setDatabase
   */
  public function testSetDatabase()
  {
    $instance = $this->object->setDatabase(new AbstractEngineStub);
    $this->assertInstanceOf('Storm\Mapper\Collection', $instance);
  }

  /**
   * @covers Storm\Mapper\Collection::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foobar');
    $this->assertInstanceOf('Storm\Mapper\Collection', $instance);
  }
}
