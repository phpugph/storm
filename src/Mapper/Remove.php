<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Mapper;

use Storm\Query\QueryInterface;
use Storm\SqlException;

use UGComponents\Event\EventTrait;

use UGComponents\Helper\InstanceTrait;
use UGComponents\Helper\LoopTrait;
use UGComponents\Helper\ConditionalTrait;

use UGComponents\Profiler\InspectorTrait;
use UGComponents\Profiler\LoggerTrait;

use UGComponents\Resolver\StateTrait;
use UGComponents\Resolver\ResolverException;

/**
 * AbstractFilter
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Remove extends AbstractMapper implements MapperInterface
{
  use EventTrait,
    InstanceTrait,
    LoopTrait,
    ConditionalTrait,
    InspectorTrait,
    LoggerTrait,
    StateTrait;

  /**
   * @const string LEFT Join type
   */
  const LEFT  = 'LEFT';

  /**
   * @const string RIGHT Join type
   */
  const RIGHT = 'RIGHT';

  /**
   * @const string INNER Join type
   */
  const INNER = 'INNER';

  /**
   * @const string OUTER Join type
   */
  const OUTER = 'OUTER';

  /**
   * @var array $join List of relational joins
   */
  protected $joins = [];

  /**
   * @var array $filter List of filters
   */
  protected $filters = [];

  /**
   * Magical processing of sortBy
   * and filterBy Methods
   *
   * @param *string $name Name of method
   * @param *array  $args Arguments to pass
   *
   * @return mixed
   */
  public function __call(string $name, array $args)
  {
    //if method starts with filterBy
    if (strpos($name, 'filterBy') === 0) {
      //ex. filterByUserName('Chris', '-')
      //choose separator
      $separator = '_';
      if (isset($args[1]) && is_scalar($args[1])) {
        $separator = (string) $args[1];
      }

      //transform method to column name
      $key = substr($name, 8);
      $key = preg_replace("/([A-Z0-9])/", $separator."$1", $key);
      $key = substr($key, strlen($separator));
      $key = strtolower($key);

      //if arg isn't set
      if (!isset($args[0])) {
        //default is null
        $args[0] = null;
      }

      //generate key
      if (is_array($args[0])) {
        $key = $key.' IN %s';
      } else {
        $key = $key.'=%s';
      }

      //add it to the search filter
      $this->addFilter($key, $args[0]);

      return $this;
    }

    try {
      return $this->__callResolver($name, $args);
    } catch (ResolverException $e) {
      throw new SqlException($e->getMessage());
    }
  }

  /**
   * Adds filter
   *
   * @param *string           $where sprintf format
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function addFilter(string $where, ...$binds): MapperInterface
  {
    $this->filters[] = [
      'where' => $where,
      'binds' => $binds
    ];

    return $this;
  }

  /**
   * Builds query based on the data given
   *
   * @return string
   */
  public function getQuery(): QueryInterface {
    $query = $this->database->getDeleteQuery()->setTable($this->table);

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

    return $query;
  }

  /**
   * Adds Inner Join On
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function innerJoinOn(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::INNER,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => false
    ];

    return $this;
  }

  /**
   * Adds Inner Join Using
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function innerJoinUsing(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::INNER,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => true
    ];

    return $this;
  }

  /**
   * Adds Left Join On
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function leftJoinOn(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::LEFT,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => false
    ];

    return $this;
  }

  /**
   * Adds Left Join Using
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function leftJoinUsing(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::LEFT,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => true
    ];

    return $this;
  }

  /**
   * Adds Outer Join On
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function outerJoinOn(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::OUTER,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => false
    ];

    return $this;
  }

  /**
   * Adds Outer Join USing
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function outerJoinUsing(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::OUTER,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => true
    ];

    return $this;
  }

  /**
   * Adds Right Join On
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function rightJoinOn(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::RIGHT,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => false
    ];

    return $this;
  }

  /**
   * Adds Right Join Using
   *
   * @param *string           $table Table name
   * @param *string           $where Table name
   * @param scalar[,scalar..] $binds sprintf values
   *
   * @return MapperInterface
   */
  public function rightJoinUsing(
    string $table,
    string $where,
    ...$binds
  ): MapperInterface
  {
    $this->joins[] = [
      'type' => static::RIGHT,
      'table' => $table,
      'where' => $where,
      'binds' => $binds,
      'using' => true
    ];

    return $this;
  }
}
