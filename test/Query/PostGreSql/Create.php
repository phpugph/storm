<?php

namespace Storm\Query\PostGreSql;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-30 at 04:38:37.
 */
class Storm_Query_PostGreSql_Create_Test extends TestCase
{
  /**
   * @var QueryCreate
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new Create('foobar');
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\Query\PostGreSql\Create::addField
   */
  public function testAddField()
  {
    $instance = $this->object->addField('foobar', array());
    $this->assertInstanceOf('Storm\Query\PostGreSql\Create', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Create::addPrimaryKey
   */
  public function testAddPrimaryKey()
  {
    $instance = $this->object->addPrimaryKey('foobar', array());
    $this->assertInstanceOf('Storm\Query\PostGreSql\Create', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Create::getQuery
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
    $this->object->addPrimaryKey('foobar');
    $actual = $this->object->getQuery();
    $this->assertEquals('CREATE TABLE "foobar" ("foobar" varchar(255) unsigned DEFAULT NULL, PRIMARY KEY ("foobar"));', $actual);
  }

  /**
   * @covers Storm\Query\PostGreSql\Create::setFields
   */
  public function testSetFields()
  {
    $instance = $this->object->setFields(array('foobar'));
    $this->assertInstanceOf('Storm\Query\PostGreSql\Create', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Create::setTable
   */
  public function testSetTable()
  {
    $instance = $this->object->setTable('foobar');
    $this->assertInstanceOf('Storm\Query\PostGreSql\Create', $instance);
  }

  /**
   * @covers Storm\Query\PostGreSql\Create::setPrimaryKeys
   */
  public function testSetPrimaryKeys()
  {
    $instance = $this->object->setPrimaryKeys(array('foobar'));
    $this->assertInstanceOf('Storm\Query\PostGreSql\Create', $instance);
  }
}
