<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Engine;

use PDO;

use Storm\Mapper\Model;
use Storm\Mapper\Collection;
use Storm\Mapper\Search;
use Storm\Mapper\Remove;
use Storm\Mapper\Update;

/**
 * Abstractly defines a layout of available methods to
 * connect to and query a database. This class also lays out
 * query building methods that auto renders a valid query
 * the specific database will understand without actually
 * needing to know the query language.
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
interface EngineInterface
{

  /**
   * Connects to the database
   *
   * @param PDO|array $options The connection options
   *
   * @return EngineInterface
   */
  public function connect($options = []): EngineInterface;

  /**
   * Binds a value and returns the bound key
   *
   * @param *string|array|number|null $value What to bind
   *
   * @return string
   */
  public function bind($value): string;

  /**
   * Returns collection
   *
   * @param array $data Initial collection data
   *
   * @return Collection
   */
  public function collection(array $data = []): Collection;

  /**
   * Removes rows that match a filter
   *
   * @param *string      $table   The table name
   * @param array|string $filters Filters to test against
   *
   * @return EngineInterface
   */
  public function deleteRows(string $table, $filters = null): EngineInterface;

  /**
   * Returns all the bound values of this query
   *
   * @return array
   */
  public function getBinds(): array;

  /**
   * Returns the connection object
   * if no connection has been made
   * it will attempt to make it
   *
   * @return resource PDO connection resource
   */
  public function getConnection(): PDO;

  /**
   * Returns the last inserted id
   *
   * @param ?string $column A particular column name
   *
   * @return int the id
   */
  public function getLastInsertedId(?string $column = null): int;

  /**
   * Returns a 1 row result given the column name and the value
   *
   * @param *string $table Table name
   * @param *string $name  Column name
   * @param ?scalar $value Column value
   *
   * @return ?array
   */
  public function getRow(string $table, string $name, $value): ?array;

  /**
   * Inserts data into a table and returns the ID
   *
   * @param *string    $table   Table name
   * @param *array     $setting Key/value array matching table columns
   * @param bool|array $bind    Whether to compute with binded variables
   *
   * @return EngineInterface
   */
  public function insertRow(
    string $table,
    array $settings,
    $bind = true
  ): EngineInterface;

  /**
   * Inserts multiple rows into a table
   *
   * @param *string    $table   Table name
   * @param array      $setting Key/value 2D array matching table columns
   * @param bool|array $bind    Whether to compute with binded variables
   *
   * @return EngineInterface
   */
  public function insertRows(
    string $table,
    array $settings,
    $bind = true
  ): EngineInterface;

  /**
   * Returns model
   *
   * @param array $data The initial data to set
   *
   * @return Model
   */
  public function model(array $data = []): Model;

  /**
   * Queries the database
   *
   * @param *string|QueryInterface $query The query to ran
   * @param array                  $binds List of binded values
   * @param ?callable              $fetch Whether to fetch all the rows
   *
   * @return array|EngineInterface
   */
  public function query($query, array $binds = [], ?callable $fetch = null);

  /**
   * Returns update
   *
   * @param ?string $table Table name
   *
   * @return Remove
   */
  public function remove(?string $table = null): Remove;

  /**
   * Returns search
   *
   * @param ?string $table Table name
   *
   * @return Search
   */
  public function search(?string $table = null): Search;

  /**
   * Sets all the bound values of this query
   *
   * @param *array $binds key/values to bind
   *
   * @return EngineInterface
   */
  public function setBinds(array $binds);

  /**
   * Returns update
   *
   * @param ?string $table Table name
   *
   * @return Update
   */
  public function update(?string $table = null): Update;

  /**
   * Updates rows that match a filter given the update settings
   *
   * @param *string    $table   Table name
   * @param *array     $setting Key/value array matching table columns
   * @param array|string $filters Filters to test against
   * @param bool|array   $bind  Whether to compute with binded variables
   *
   * @return EngineInterface
   */
  public function updateRows(
    string $table,
    array $settings,
    $filters = null,
    $bind = true
  ): EngineInterface;
}
