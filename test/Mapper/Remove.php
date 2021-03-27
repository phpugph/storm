<?php

namespace Storm\Mapper;

use StdClass;
use PHPUnit\Framework\TestCase;

use Storm\SqlException;
use Storm\Engine\AbstractEngine;
use Storm\Engine\EngineInterface;

use UGComponents\Resolver\ResolverHandler;
use UGComponents\Event\EventEmitter;
use UGComponents\Profiler\InspectorHandler;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class Storm_Mapper_Remove_Test extends TestCase
{
  /**
   * @var Search
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Search(new AbstractEngineStub);
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Mapper\Remove::__construct
   */
  public function test__construct()
  {
    $actual = $this->object->__construct(new AbstractEngineStub);

    $this->assertNull($actual);
  }

  /**
   * @covers Storm\Mapper\Remove::__call
   */
  public function test__call()
  {
    $instance = $this->object->filterByFoo('bar', '_');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
    $instance = $this->object->filterByFoo();
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
    $instance = $this->object->filterByFoo([1, 2, 3]);
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);

    $instance = $this->object->sortByFoo('ASC', '_');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
    $instance = $this->object->sortByFoo();
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);

    $trigger = false;
    try {
      $this->object->foobar();
    } catch(SqlException $e) {
      $trigger = true;
    }

    $this->assertTrue($trigger);
  }

  /**
   * @covers Storm\Mapper\Remove::addFilter
   */
  public function testAddFilter()
  {
    $instance = $this->object->addFilter('foo=1');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);

    $instance = $this->object->addFilter('foo=%s', 1);
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::innerJoinOn
   */
  public function testInnerJoinOn()
  {
    $instance = $this->object->innerJoinOn('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::innerJoinUsing
   */
  public function testInnerJoinUsing()
  {
    $instance = $this->object->innerJoinUsing('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::leftJoinOn
   */
  public function testLeftJoinOn()
  {
    $instance = $this->object->leftJoinOn('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::leftJoinUsing
   */
  public function testLeftJoinUsing()
  {
    $instance = $this->object->leftJoinUsing('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::outerJoinOn
   */
  public function testOuterJoinOn()
  {
    $instance = $this->object->outerJoinOn('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::outerJoinUsing
   */
  public function testOuterJoinUsing()
  {
    $instance = $this->object->outerJoinUsing('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::rightJoinOn
   */
  public function testRightJoinOn()
  {
    $instance = $this->object->rightJoinOn('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::rightJoinUsing
   */
  public function testRightJoinUsing()
  {
    $instance = $this->object->rightJoinUsing('bar', 'bar_id=foo_id');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foo');
    $this->assertInstanceOf('Storm\Mapper\Remove', $instance);
  }

  /**
   * @covers Storm\Mapper\Remove::getEventEmitter
   */
  public function testGetEventEmitter()
  {
    $instance = $this->object->getEventEmitter();
    $this->assertInstanceOf('UGComponents\Event\EventEmitter', $instance);
  }
}

if(!class_exists('Storm\Mapper\AbstractEngineStub')) {
  class AbstractEngineStub extends AbstractEngine implements EngineInterface
  {
    public function connect($options = []): EngineInterface
    {
      $this->connection = 'foobar';
      return $this;
    }

    public function getLastInsertedId(?string $column = null): int
    {
      return 123;
    }

    public function query($query, array $binds = [], ?callable $fetch = null)
    {
      return array(array(
        'total' => 123,
        'query' => (string) $query,
        'binds' => $binds
      ));
    }

    public function getColumns()
    {
      return array(
        array(
          'Field' => 'foobar_id',
          'Type' => 'int',
          'Key' => 'PRI',
          'Default' => null,
          'Null' => 1
        ),
        array(
          'Field' => 'foobar_title',
          'Type' => 'vachar',
          'Key' => null,
          'Default' => null,
          'Null' => 1
        ),
        array(
          'Field' => 'foobar_date',
          'Type' => 'datetime',
          'Key' => null,
          'Default' => null,
          'Null' => 1
        )
      );
    }
  }
}