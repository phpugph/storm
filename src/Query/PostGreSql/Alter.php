<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Query\PostGreSql;

use Storm\Query\QueryInterface;
use Storm\Query\AbstractQuery;

/**
 * Generates alter query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Alter extends AbstractQuery implements QueryInterface
{
  /**
   * @var array $changeFields List of fields to change
   */
  protected $changeFields = [];

  /**
   * @var array $addFields List of fields to add
   */
  protected $addFields = [];

  /**
   * @var array $removeFields List of fields to remove
   */
  protected $removeFields = [];

  /**
   * @var array $addPrimaryKeys List of primary keys to add
   */
  protected $addPrimaryKeys = [];

  /**
   * @var array $removePrimaryKeys List of primary keys to remove
   */
  protected $removePrimaryKeys = [];

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
   * @return QueryAlter
   */
  public function addField($name, array $attributes)
  {
    $this->addFields[$name] = $attributes;
    return $this;
  }

  /**
   * Adds a primary key
   *
   * @param *string $name Name of key
   *
   * @return QueryAlter
   */
  public function addPrimaryKey($name)
  {
    $this->addPrimaryKeys[] = '"'.$name.'"';
    return $this;
  }

  /**
   * Changes attributes of the table given
   * the field name
   *
   * @param *string $name     Column name
   * @param *array  $attributes Column attributes
   *
   * @return QueryAlter
   */
  public function changeField($name, array $attributes)
  {
    $this->changeFields[$name] = $attributes;
    return $this;
  }

  /**
   * Returns the string version of the query
   *
   * @return string
   */
  public function getQuery(): string
  {
    $fields = [];
    $table = '"'.$this->table.'"';

    foreach ($this->removeFields as $name) {
      $fields[] = 'DROP COLUMN "'.$name.'"';
    }

    foreach ($this->addFields as $name => $attr) {
      $field = ['ADD "'.$name.'"'];
      if (isset($attr['type'])) {
        $field[] = isset($attr['length']) ? $attr['type']
        . '('.$attr['length'].')' :
        $attr['type'];

        if (isset($attr['list']) && $attr['list']) {
          $field[count($field)-1].='[]';
        }
      }

      if (isset($attr['attribute'])) {
        $field[] = $attr['attribute'];
      }

      if (isset($attr['unique']) && $attr['unique']) {
        $field[] = 'UNIQUE';
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

    foreach ($this->changeFields as $name => $attr) {
      $field = ['ALTER COLUMN "'.$name.'"'];

      if (isset($attr['name'])) {
        $field = ['CHANGE "'.$name.'"  "'.$attr['name'].'"'];
      }

      if (isset($attr['type'])) {
        $field[] = isset($attr['length']) ?
        $attr['type'] . '('.$attr['length'].')' :
        $attr['type'];

        if (isset($attr['list']) && $attr['list']) {
          $field[count($field)-1].='[]';
        }
      }

      if (isset($attr['attribute'])) {
        $field[] = $attr['attribute'];
      }

      if (isset($attr['unique']) && $attr['unique']) {
        $field[] = 'UNIQUE';
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

    foreach ($this->removePrimaryKeys as $key) {
      $fields[] = 'DROP PRIMARY KEY "'.$key.'"';
    }

    if (!empty($this->addPrimaryKeys)) {
      $fields[] = 'ADD PRIMARY KEY ('.implode(', ', $this->addPrimaryKeys).')';
    }

    $fields = implode(", \n", $fields);

    return sprintf('ALTER TABLE %s %s;', $table, $fields);
  }

  /**
   * Removes a field
   *
   * @param *string $name Name of field
   *
   * @return QueryAlter
   */
  public function removeField($name)
  {
    $this->removeFields[] = $name;
    return $this;
  }

  /**
   * Removes a primary key
   *
   * @param *string $name Name of key
   *
   * @return QueryAlter
   */
  public function removePrimaryKey($name)
  {
    $this->removePrimaryKeys[] = $name;
    return $this;
  }
}
