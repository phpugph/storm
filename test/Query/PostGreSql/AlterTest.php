<?php

namespace Storm\Query\PostGreSql;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-30 at 04:38:37.
 */
class AlterTest extends TestCase
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
   * @covers Storm\Query\PostGreSql\Alter::__construct
   */
  public function test__construct()
  {
    $actual = $this->object->__construct('foobar');

    $this->assertNull($actual);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::addField
   */
  public function testAddField()
  {
    $instance = $this->object->addField('foobar', array());
    $this->assertInstanceOf('Storm\Query\PostGreSql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::addPrimaryKey
   */
  public function testAddPrimaryKey()
  {
    $instance = $this->object->addPrimaryKey('foobar');
    $this->assertInstanceOf('Storm\Query\PostGreSql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::changeField
   */
  public function testChangeField()
  {
    $instance = $this->object->changeField('foobar', array());
    $this->assertInstanceOf('Storm\Query\PostGreSql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::getQuery
   */
  public function testGetQuery()
  {

    $actual = $this->object->getQuery();
    $this->assertEquals('ALTER TABLE "foobar" ;', $actual);

    $actual = $this->object
      ->addPrimaryKey('foobar')
      ->changeField('foobar', array())
      ->removeField('foobar')
      ->removePrimaryKey('foobar')
      ->setTable('foobar')
      ->getQuery();

    $this->assertEquals('ALTER TABLE "foobar" DROP COLUMN "foobar", ' . "\n"
    . 'ALTER COLUMN "foobar", ' . "\n"
    . 'DROP PRIMARY KEY "foobar", ' . "\n"
    . 'ADD PRIMARY KEY ("foobar");', $actual);

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

    $actual = $this->object->getQuery();
    $this->assertEquals('ALTER TABLE "foobar" DROP COLUMN "foobar", ' . "\n"
      . 'ADD "foobar" varchar(255) unsigned DEFAULT NULL, ' . "\n"
      . 'ALTER COLUMN "foobar" varchar(255) unsigned DEFAULT NULL, ' . "\n"
      . 'DROP PRIMARY KEY "foobar", ' . "\n"
      . 'ADD PRIMARY KEY ("foobar");', $actual);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::removeField
   */
  public function testRemoveField()
  {
    $instance = $this->object->removeField('foobar');
    $this->assertInstanceOf('Storm\Query\PostGreSql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::removePrimaryKey
   */
  public function testRemovePrimaryKey()
  {
    $instance = $this->object->removePrimaryKey('foobar');
    $this->assertInstanceOf('Storm\Query\PostGreSql\Alter', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Alter::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foobar');
    $this->assertInstanceOf('Storm\Query\PostGreSql\Alter', $instance);
  }
}
