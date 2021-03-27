<?php

namespace Storm\Query\MySql;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:03.
 */
class Storm_Query_MySql_Alter_Test extends TestCase
{
  /**
   * @var QueryAlter
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Alter('foobar');
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Query\MySql\Alter::__construct
   */
  public function test__construct()
  {
    $actual = $this->object->__construct('foobar');

    $this->assertNull($actual);
  }

  /**
   * @covers Storm\Query\MySql\Alter::addField
   */
  public function testAddField()
  {
    $instance = $this->object->addField('foobar', array());
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::addKey
   */
  public function testAddKey()
  {
    $instance = $this->object->addKey('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::addPrimaryKey
   */
  public function testAddPrimaryKey()
  {
    $instance = $this->object->addPrimaryKey('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::addUniqueKey
   */
  public function testAddUniqueKey()
  {
    $instance = $this->object->addUniqueKey('foobar', array());
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::changeField
   */
  public function testChangeField()
  {
    $instance = $this->object->changeField('foobar', array());
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::getQuery
   */
  public function testGetQuery()
  {
    $this->object->addField('foobar', array(
      'type'    => 'varchar',
      'default'  => 'something',
      'null'    => true,
      'attribute'  => 'unsigned',
      'length'  => 255
    ));

    $this->object->changeField('foobar', array(
      'type'    => 'varchar',
      'default'  => 'something',
      'null'    => true,
      'attribute'  => 'unsigned',
      'length'  => 255
    ));

    $this->object->addPrimaryKey('foobar');
    $this->object->addUniqueKey('foobar', array());
    $this->object->addKey('foobar');
    $this->object->removeField('foobar');
    $this->object->removeKey('foobar');
    $this->object->addPrimaryKey('foobar');
    $this->object->removeUniqueKey('foobar');
    $actual = $this->object->getQuery();
    $this->assertEquals('ALTER TABLE `foobar` DROP `foobar`, ' . "\n"
    . 'ADD `foobar` varchar(255) unsigned DEFAULT NULL, ' . "\n"
    . 'CHANGE `foobar`  `foobar` varchar(255) unsigned DEFAULT NULL, ' . "\n"
    . 'DROP INDEX `foobar`, ' . "\n"
    . 'ADD INDEX (`foobar`), ' . "\n"
    . 'DROP INDEX `foobar`, ' . "\n"
    . 'ADD UNIQUE (`foobar`), ' . "\n"
    . 'ADD PRIMARY KEY (`foobar`, `foobar`);', $actual);
  }

  /**
   * @covers Storm\Query\MySql\Alter::removeField
   */
  public function testRemoveField()
  {
    $instance = $this->object->removeField('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::removeKey
   */
  public function testRemoveKey()
  {
    $instance = $this->object->removeKey('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::removePrimaryKey
   */
  public function testRemovePrimaryKey()
  {
    $instance = $this->object->removePrimaryKey('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::removeUniqueKey
   */
  public function testRemoveUniqueKey()
  {
    $instance = $this->object->removeUniqueKey('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\MySql\Alter::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foobar');
    $this->assertInstanceOf('Storm\Query\MySql\Alter', $instance);
  }
}
