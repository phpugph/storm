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
class Storm_Mapper_Search_Test extends TestCase
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
   * @covers Storm\Mapper\Search::__construct
   */
  public function test__construct()
  {
    $actual = $this->object->__construct(new AbstractEngineStub);

    $this->assertNull($actual);
  }

  /**
   * @covers Storm\Mapper\Search::__call
   */
  public function test__call()
  {
    $instance = $this->object->filterByFoo('bar', '_');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
    $instance = $this->object->filterByFoo();
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
    $instance = $this->object->filterByFoo([1, 2, 3]);
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);

    $instance = $this->object->sortByFoo('ASC', '_');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
    $instance = $this->object->sortByFoo();
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);

    $trigger = false;
    try {
      $this->object->foobar();
    } catch(SqlException $e) {
      $trigger = true;
    }

    $this->assertTrue($trigger);
  }

  /**
   * @covers Storm\Mapper\Search::addSort
   */
  public function testAddSort()
  {
    $instance = $this->object->addSort('bar', 'ASC');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::getCollection
   */
  public function testGetCollection()
  {
    $instance = $this->object->getCollection();
    $this->assertInstanceOf('Storm\Mapper\Collection', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::getModel
   */
  public function testGetModel()
  {
    $instance = $this->object->getModel();
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::getRow
   */
  public function testGetRow()
  {
    $actual = $this->object->getRow();
    $this->assertEquals('SELECT * FROM;', $actual['query']);

    $actual = $this->object->getRow(0, 'foobar');
    $this->assertNull($actual);
  }

  /**
   * @covers Storm\Mapper\Search::getRows
   */
  public function testGetRows()
  {
    $this->object->groupBy('foo');
    $this->object->setRange(4);
    $this->object->addSort('bar', 'ASC');
    $actual = $this->object->getRows();
    $this->assertEquals('SELECT * FROM  GROUP BY foo ORDER BY bar ASC LIMIT 0, 4;', $actual[0]['query']);
  }

  /**
   * @covers Storm\Mapper\Search::getTotal
   */
  public function testGetTotal()
  {
    $actual = $this->object->getTotal();
    $this->assertEquals(123, $actual);
  }

  /**
   * @covers Storm\Mapper\Search::groupBy
   */
  public function testGroupBy()
  {
    $instance = $this->object->groupBy('foo');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::having
   */
  public function testHaving()
  {
    $instance = $this->object->having('foo');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::setColumns
   */
  public function testSetColumns()
  {
    $instance = $this->object->setColumns('foo', 'bar');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::setPage
   */
  public function testSetPage()
  {
    $instance = $this->object->setPage(-4);
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::setRange
   */
  public function testSetRange()
  {
    $instance = $this->object->setRange(-4);
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::setStart
   */
  public function testSetStart()
  {
    $instance = $this->object->setStart(4);
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
  }

  /**
   * @covers Storm\Mapper\Search::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foo');
    $this->assertInstanceOf('Storm\Mapper\Search', $instance);
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
