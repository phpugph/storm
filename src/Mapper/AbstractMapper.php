<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Mapper;

use Storm\Query\QueryInterface;
use Storm\Engine\EngineInterface;

/**
 * AbstractFilter
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
abstract class AbstractMapper
{
  /**
  * @var EngineInterface $database = null Database object
  */
  protected $database = null;

  /**
   * @var ?string $table Table name
   */
  protected $table = null;

  /**
   * Construct: Store database
   *
   * @param *EngineInterface $database Database object
   */
  public function __construct(EngineInterface $database)
  {
    $this->database = $database;
  }

  /**
   * returns the database
   *
   * @param *string $table Table class name
   *
   * @return EngineInterface
   */
  public function getDatabase(): EngineInterface
  {
    return $this->database;
  }

  /**
   * Builds query based on the data given
   *
   * @return string
   */
  abstract public function getQuery(): QueryInterface;

  /**
   * Queries the database
   *
   * @param ?callable $fetch Whether to fetch all the rows
   *
   * @return array|MapperInterface
   */
  public function query(?callable $fetch = null)
  {
    $query = $this->getQuery();

    $rows = $this->database->query($query, $this->database->getBinds(), $fetch);

    if (!$fetch) {
      return $rows;
    }

    return $this;
  }

  /**
   * Sets Table Name
   *
   * @param *string $table Table class name
   *
   * @return MapperInterface
   */
  public function setTable(string $table): MapperInterface
  {
    $this->table = $table;
    return $this;
  }
}
