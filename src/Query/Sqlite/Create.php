<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\Sqlite;

use Storm\Query\QueryInterface;
use Storm\Query\AbstractQuery;

/**
 * Generates create table query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Create extends AbstractQuery implements QueryInterface
{
  /**
   * @var ?string $comments Table comments
   */
  protected $comments = null;

  /**
   * @var array $fields List of fields
   */
  protected $fields = [];

  /**
   * @var array $keys List of key indexes
   */
  protected $keys = [];

  /**
   * @var array $uniqueKeys List of unique keys
   */
  protected $uniqueKeys = [];

  /**
   * @var array $primaryKeys List of primary keys
   */
  protected $primaryKeys = [];

  /**
   * Construct: set table name, if given
   *
   * @param ?string $table Table name
   */
  public function __construct(?string $table = null)
  {
    if (is_string($table)) {
      $this->setTable($table);
    }
  }

  /**
   * Adds a field in the table
   *
   * @param *string $name     Column name
   * @param *array  $attributes Column attributes
   *
   * @return QueryInterface
   */
  public function addField(string $name, array $attributes): QueryInterface
  {
    $this->fields[$name] = $attributes;
    return $this;
  }

  /**
   * Adds an index key
   *
   * @param *string $name  Name of column
   * @param *string $table Name of foreign table
   * @param *string $key   Name of key
   *
   * @return QueryInterface
   */
  public function addForeignKey(string $name, string $table, string $key): QueryInterface
  {
    $this->keys[$name] = [$table, $key];
    return $this;
  }

  /**
   * Adds a unique key
   *
   * @param *string $name   Name of key
   * @param *array  $fields List of key fields
   *
   * @return QueryInterface
   */
  public function addUniqueKey(string $name, array $fields): QueryInterface
  {
    $this->uniqueKeys[$name] = $fields;
    return $this;
  }

  /**
   * Returns the string version of the query
   *
   * @return string
   */
  public function getQuery(): string
  {
    $table = '"'.$this->table.'"';

    $fields = [];
    foreach ($this->fields as $name => $attr) {
      $field = ['"'.$name.'"'];
      if (isset($attr['type'])) {
        $field[] = isset($attr['length']) ?
          $attr['type'] . '('.$attr['length'].')' :
          $attr['type'];
      }

      if (isset($attr['primary'])) {
        $field[] = 'PRIMARY KEY';
      }

      if (isset($attr['attribute'])) {
        $field[] = $attr['attribute'];
      }

      if (isset($attr['null'])) {
        if ($attr['null'] == false) {
          $field[] = 'NOT NULL';
        } else {
          $field[] = 'DEFAULT NULL';
        }
      }

      if (isset($attr['default'])&& $attr['default'] !== false) {
        if (!isset($attr['null']) || $attr['null'] == false) {
          if (is_string($attr['default'])) {
            $field[] = 'DEFAULT \''.$attr['default'] . '\'';
          } else if (is_numeric($attr['default'])) {
            $field[] = 'DEFAULT '.$attr['default'];
          }
        }
      }

      $fields[] = implode(' ', $field);
    }

    $fields = !empty($fields) ? implode(', ', $fields) : '';

    $uniques = [];
    foreach ($this->uniqueKeys as $key => $value) {
      $uniques[] = 'UNIQUE "'. $key .'" ("'.implode('", "', $value).'")';
    }

    $uniques = !empty($uniques) ? ', ' . implode(", \n", $uniques) : '';

    $keys = [];
    foreach ($this->keys as $key => $value) {
      $keys[] = 'FOREIGN KEY "'. $key .'" REFERENCES '.$value[0].'('.$value[1].')';
    }

    $keys = !empty($keys) ? ', ' . implode(", \n", $keys) : '';

    return sprintf(
      'CREATE TABLE %s (%s%s%s);',
      $table,
      $fields,
      $uniques,
      $keys
    );
  }

  /**
   * Sets comments
   *
   * @param *string $comments Table comments
   *
   * @return QueryInterface
   */
  public function setComments(string $comments): QueryInterface
  {
    $this->comments = $comments;
    return $this;
  }

  /**
   * Sets a list of fields to the table
   *
   * @param *array $fields List of fields
   *
   * @return QueryInterface
   */
  public function setFields(array $fields): QueryInterface
  {
    $this->fields = $fields;
    return $this;
  }

  /**
   * Sets a list of keys to the table
   *
   * @param *array $keys A list of foreign keys
   *
   * @return QueryInterface
   */
  public function setForiegnKeys(array $keys): QueryInterface
  {
    $this->keys = $keys;
    return $this;
  }

  /**
   * Sets a list of unique keys to the table
   *
   * @param *array $uniqueKeys List of unique keys
   *
   * @return QueryInterface
   */
  public function setUniqueKeys(array $uniqueKeys): QueryInterface
  {
    $this->uniqueKeys = $uniqueKeys;
    return $this;
  }
}
