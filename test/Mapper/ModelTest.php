<?php

namespace Storm\Mapper;

use PHPUnit\Framework\TestCase;

use Storm\SqlException;

use Storm\Engine\AbstractEngine;
use Storm\Engine\EngineInterface;

use UGComponents\Helper\InstanceTrait;
use UGComponents\Helper\LoopTrait;
use UGComponents\Helper\ConditionalTrait;

use UGComponents\Profiler\InspectorTrait;
use UGComponents\Profiler\LoggerTrait;

use UGComponents\Resolver\StateTrait;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class ModelTest extends TestCase
{
  /**
   * @var Model
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Model();
    $this->object
      ->setDatabase(new AbstractEngineStub)
      ->setFoobarTitle('Foo Bar 1')
      ->setFoobarDate('January 12, 2015')
      ->setFooDate(1234567890);
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Mapper\Model::formatTime
   */
  public function testFormatTime()
  {
    $actual = $this->object->formatTime('foobar_date');
    $this->assertEquals('2015-01-12 12:00:00', $actual->getFoobarDate());
    $actual = $this->object->formatTime('foo_date');
    $this->assertEquals('2009-02-13 11:31:30', $actual->getFooDate());
    $actual = $this->object->formatTime('foo_title');
    $this->assertEquals('Foo Bar 1', $actual->getFoobarTitle());
  }

  /**
   * @covers Storm\Mapper\Model::insert
   * @covers Storm\Mapper\Model::getMeta
   * @covers Storm\Mapper\Model::getValidColumns
   */
  public function testInsert()
  {
    $instance = $this->object->insert('foo');
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);
    $this->assertEquals(123, $this->object->getFoobarId());

    $instance = $this->object->setTable('foo')->insert();
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);
    $this->assertEquals(123, $this->object->getFoobarId());

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->insert();
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->insert('foo');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->insert('foo', new AbstractEngineStub);
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertFalse($triggered);
  }

  /**
   * @covers Storm\Mapper\Model::remove
   * @covers Storm\Mapper\Model::getMeta
   * @covers Storm\Mapper\Model::getValidColumns
   */
  public function testRemove()
  {
    $instance = $this->object->setFoobarId(321)->remove('foo');
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);

    $instance = $this->object->setFoobarId(321)->setTable('foo')->remove();
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarId(321)
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->remove();
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarId(321)
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->remove('foo');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarId(321)
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->remove('foo', new AbstractEngineStub, 'foobar_id');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertFalse($triggered);
  }

  /**
   * @covers Storm\Mapper\Model::save
   * @covers Storm\Mapper\Model::isPrimarySet
   */
  public function testSave()
  {
    $instance = $this->object->save('foo');
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);

    $instance = $this->object->setTable('foo')->save();
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->save();
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->save('foo');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarId(321)
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->save('foo', new AbstractEngineStub, 'foobar_id');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertFalse($triggered);
  }

  /**
   * @covers Storm\Mapper\Model::setDatabase
   */
  public function testSetDatabase()
  {
    $instance = $this->object->setDatabase(new AbstractEngineStub);
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);
  }

  /**
   * @covers Storm\Mapper\Model::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foo');
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);
  }

  /**
   * @covers Storm\Mapper\Model::update
   * @covers Storm\Mapper\Model::getMeta
   * @covers Storm\Mapper\Model::getValidColumns
   */
  public function testUpdate()
  {
    $instance = $this->object->setFoobarId(321)->update('foo');
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);

    $instance = $this->object->setFoobarId(321)->setTable('foo')->update();
    $this->assertInstanceOf('Storm\Mapper\Model', $instance);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->update();
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->update('foo');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertTrue($triggered);

    $triggered = false;
    try {
      $this->object = new Model();
      $this->object
        ->setFoobarId(321)
        ->setFoobarTitle('Foo Bar 1')
        ->setFoobarDate('January 12, 2015')
        ->setFooDate(1234567890)
        ->update('foo', new AbstractEngineStub, 'foobar_id');
    } catch(SqlException $e) {
      $triggered = true;
    }

    $this->assertFalse($triggered);
  }
}

if(!class_exists('Storm\Mapper\AbstractEngineStub')) {
  class AbstractEngineStub extends AbstractEngine implements EngineInterface
  {
    use InstanceTrait,
      LoopTrait,
      ConditionalTrait,
      InspectorTrait,
      LoggerTrait,
      StateTrait
      {
        StateTrait::__callResolver as __call;
    }

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
