<?php 

namespace Storm;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-07-27 at 02:11:02.
 */
class SqlExceptionTest extends TestCase
{
  /**
   * @var SqlException
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(): void
  {
    $this->object = new SqlException;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(): void
  {
  }

  /**
   * @covers Storm\SqlException::forQueryError
   */
  public function testForQueryError()
  {
		$message = null;
		try {
			throw SqlException::forQueryError('foo', 'bar');
		} catch(SqlException $e) {
			$message = $e->getMessage();
		}
		
		$this->assertEquals('foo Query: bar', $message);
  }

  /**
   * @covers Storm\SqlException::forTableNotSet
   */
  public function testForTableNotSet()
  {
		$message = null;
		try {
			throw SqlException::forTableNotSet();
		} catch(SqlException $e) {
			$message = $e->getMessage();
		}
		
		$this->assertEquals('No default table set or was passed.', $message);
  }

  /**
   * @covers Storm\SqlException::forDatabaseNotSet
   */
  public function testForDatabaseNotSet()
  {
		$message = null;
		try {
			throw SqlException::forDatabaseNotSet();
		} catch(SqlException $e) {
			$message = $e->getMessage();
		}
		
		$this->assertEquals('No default database set or was passed.', $message);
  }

  /**
   * @covers Storm\SqlException::forUnknownPDO
   */
  public function testForUnknownPDO()
  {
		$message = null;
		try {
			throw SqlException::forUnknownPDO('foo');
		} catch(SqlException $e) {
			$message = $e->getMessage();
		}
		
		$this->assertEquals('Could not match an SQL handler with foo', $message);
  }
}
