<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\MySql;

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
   * @param *string $name       Column name
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
   * @param *string $name   Name of key
   * @param *array  $fields List of key fields
   *
   * @return QueryInterface
   */
  public function addKey(string $name, array $fields): QueryInterface
  {
    $this->keys[$name] = $fields;
    return $this;
  }

  /**
   * Adds a primary key
   *
   * @param *string $name Name of key
   *
   * @return QueryInterface
   */
  public function addPrimaryKey(string $name): QueryInterface
  {
    $this->primaryKeys[] = $name;
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
    $table = '`'.$this->table.'`';

    $fields = [];
    foreach ($this->fields as $name => $attr) {
      $field = ['`'.$name.'`'];
      if (isset($attr['type'])) {
        $field[] = isset($attr['length']) && $attr['length'] ?
          $attr['type'] . '('.$attr['length'].')' :
          $attr['type'];
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
            if (preg_match('/[a-zA-Z0-9_]+\([^\)]*\)/is', $attr['default'])) {
              $field[] = 'DEFAULT '.$attr['default'];
            } else {
              $field[] = 'DEFAULT \''.$attr['default'] . '\'';
            }
          } else if (is_numeric($attr['default'])) {
            $field[] = 'DEFAULT '.$attr['default'];
          }
        }
      }

      if (isset($attr['auto_increment']) && $attr['auto_increment'] == true) {
        $field[] = 'auto_increment';
      }

      $fields[] = implode(' ', $field);
    }

    $fields = !empty($fields) ? implode(', ', $fields) : '';

    $primary = !empty($this->primaryKeys) ?
      ', PRIMARY KEY (`'.implode('`, `', $this->primaryKeys).'`)' :
      '';

    $uniques = [];
    foreach ($this->uniqueKeys as $key => $value) {
      $uniques[] = 'UNIQUE KEY `'. $key .'` (`'.implode('`, `', $value).'`)';
    }

    $uniques = !empty($uniques) ? ', ' . implode(", \n", $uniques) : '';

    $keys = [];
    foreach ($this->keys as $key => $value) {
      $keys[] = 'KEY `'. $key .'` (`'.implode('`, `', $value).'`)';
    }

    $keys = !empty($keys) ? ', ' . implode(", \n", $keys) : '';

    return sprintf(
      'CREATE TABLE %s (%s%s%s%s);',
      $table,
      $fields,
      $primary,
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
   * @param *array $keys List of keys
   *
   * @return QueryInterface
   */
  public function setKeys(array $keys): QueryInterface
  {
    $this->keys = $keys;
    return $this;
  }

  /**
   * Sets a list of primary keys to the table
   *
   * @param *array $primaryKeys List of primary keys
   *
   * @return QueryInterface
   */
  public function setPrimaryKeys(array $primaryKeys): QueryInterface
  {
    $this->primaryKeys = $primaryKeys;
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
