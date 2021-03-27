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
class Update extends Remove
{
  use EventTrait,
    InstanceTrait,
    LoopTrait,
    ConditionalTrait,
    InspectorTrait,
    LoggerTrait,
    StateTrait;

  /**
   * @var array $settings List of settings
   */
  protected $settings = [];

  /**
   * Builds query based on the data given
   *
   * @return string
   */
  public function getQuery(): QueryInterface
  {
    $query = $this->database->getUpdateQuery()->setTable($this->table);

    foreach ($this->joins as $join) {
      $where = $join['where'];
      if (!empty($join['binds'])) {
        foreach ($join['binds'] as $i => $value) {
          $join['binds'][$i] = $this->database->bind($value);
        }

        $where = vsprintf($where, $join['binds']);
      }

      $query->join($join['type'], $join['table'], $where, $join['using']);
    }

    foreach ($this->filters as $i => $filter) {
      //array('post_id=%s AND post_title IN %s', 123, array('asd'));
      $where = $filter['where'];
      if (!empty($filter['binds'])) {
        foreach ($filter['binds'] as $i => $value) {
          $filter['binds'][$i] = $this->database->bind($value);
        }

        $where = vsprintf($where, $filter['binds']);
      }

      $query->where($where);
    }

    foreach ($this->settings as $key => $setting) {
      $value = $setting['value'];
      if ($setting['bind']) {
        $value = $this->database->bind($value);
      }

      $query->set($key, $value);
    }

    return $query;
  }

  /**
   * Key/Value setter
   *
   * @param *string $key column name
   * @param *string $value
   * @param bool    $bind  Whhether to bind the value
   *
   * @return MapperInterface
   */
  public function set(string $key, string $value, bool $bind = true): MapperInterface
  {
    $this->settings[$key] = [
      'value' => $value,
      'bind' => $bind
    ];

    return $this;
  }
}
