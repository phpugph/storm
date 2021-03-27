<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Mapper;

use Storm\Query\QueryInterface;

use UGComponents\Event\EventTrait;

use UGComponents\Helper\InstanceTrait;
use UGComponents\Helper\LoopTrait;
use UGComponents\Helper\ConditionalTrait;

use UGComponents\Profiler\InspectorTrait;
use UGComponents\Profiler\LoggerTrait;

use UGComponents\Resolver\StateTrait;
use UGComponents\Resolver\ResolverException;

/**
 * Sql Search
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Insert extends AbstractMapper implements MapperInterface
{
  use EventTrait,
    InstanceTrait,
    LoopTrait,
    ConditionalTrait,
    InspectorTrait,
    LoggerTrait,
    StateTrait;

  /**
   * @var array $rows List of rows to be inserted
   */
  protected $rows = [];

  /**
   * Builds query based on the data given
   *
   * @return string
   */
  public function getQuery(): QueryInterface
  {
    $query = $this->database->getInsertQuery()->setTable($this->table);

    $rows = $this->rows;
    ksort($rows);
    $rows = array_values($rows);

    foreach ($rows as $index => $row) {
      foreach ($row as $key => $setting) {
        $value = $setting['value'];
        if ($setting['bind']) {
          $value = $this->database->bind($value);
        }

        $query->set($key, $value, $index);
      }
    }

    return $query;
  }

  /**
   * Key/Value setter
   *
   * @param *string $key    column name
   * @param *string $value
   * @param bool    $bind   Whether to bind the value
   * @param int     $index  row index
   *
   * @return MapperInterface
   */
  public function set(
    string $key,
    string $value,
    bool $bind = true,
    int $index = 0
  ): MapperInterface
  {
    $this->rows[$index][$key] = [
      'value' => $value,
      'bind' => $bind
    ];

    return $this;
  }
}
