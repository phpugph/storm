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
   * @var array $addKeys List of keys to add
   */
  protected $addKeys = [];

  /**
   * @var array $removeKeys List of keys to remove
   */
  protected $removeKeys = [];

  /**
   * @var array $addUniqueKeys List of unique keys to add
   */
  protected $addUniqueKeys = [];

  /**
   * @var array $removeUniqueKeys List of unique keys to remove
   */
  protected $removeUniqueKeys = [];

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
   * @param *string $name       Column name
   * @param *array  $attributes Column attributes
   *
   * @return QueryInterface
   */
  public function addField(string $name, array $attributes): QueryInterface
  {
    $this->addFields[$name] = $attributes;
    return $this;
  }

  /**
   * Adds an index key
   *
   * @param *string $name Name of key
   *
   * @return QueryInterface
   */
  public function addKey(string $name): QueryInterface
  {
    $this->addKeys[] = '`'.$name.'`';
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
    $this->addPrimaryKeys[] = '`'.$name.'`';
    return $this;
  }

  /**
   * Adds a unique key
   *
   * @param *string $name Name of key
   *
   * @return QueryInterface
   */
  public function addUniqueKey(string $name): QueryInterface
  {
    $this->addUniqueKeys[] = '`'.$name.'`';
    return $this;
  }

  /**
   * Changes attributes of the table given
   * the field name
   *
   * @param *string $name     Column name
   * @param *array  $attributes Column attributes
   *
   * @return QueryInterface
   */
  public function changeField(string $name, array $attributes): QueryInterface
  {
    $this->changeFields[$name] = $attributes;
    return $this;
  }

  /**
   * Returns the string version of the query
   *
   * @param bool $unbind Whether to unbind variables
   *
   * @return string
   */
  public function getQuery(bool $unbind = false): string
  {
    $fields = [];
    $table = '`'.$this->table.'`';

    foreach ($this->removeFields as $name) {
      $fields[] = 'DROP `'.$name.'`';
    }

    foreach ($this->addFields as $name => $attr) {
      $field = ['ADD `'.$name.'`'];
      if (isset($attr['type'])) {
        $field[] = isset($attr['length']) ?
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
            $field[] = 'DEFAULT \''.$attr['default'] . '\'';
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

    foreach ($this->changeFields as $name => $attr) {
      $field = ['CHANGE `'.$name.'`  `'.$name.'`'];

      if (isset($attr['name'])) {
        $field = ['CHANGE `'.$name.'`  `'.$attr['name'].'`'];
      }

      if (isset($attr['type'])) {
        $field[] = isset($attr['length']) ? $attr['type'] . '('.$attr['length'].')' : $attr['type'];
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

      if (isset($attr['auto_increment']) && $attr['auto_increment'] == true) {
        $field[] = 'auto_increment';
      }

      $fields[] = implode(' ', $field);
    }

    foreach ($this->removeKeys as $key) {
      $fields[] = 'DROP INDEX `'.$key.'`';
    }

    if (!empty($this->addKeys)) {
      $fields[] = 'ADD INDEX ('.implode(', ', $this->addKeys).')';
    }

    foreach ($this->removeUniqueKeys as $key) {
      $fields[] = 'DROP INDEX `'.$key.'`';
    }

    if (!empty($this->addUniqueKeys)) {
      $fields[] = 'ADD UNIQUE ('.implode(', ', $this->addUniqueKeys).')';
    }

    foreach ($this->removePrimaryKeys as $key) {
      $fields[] = 'DROP PRIMARY KEY `'.$key.'`';
    }

    if (!empty($this->addPrimaryKeys)) {
      $fields[] = 'ADD PRIMARY KEY ('.implode(', ', $this->addPrimaryKeys).')';
    }

    $fields = implode(", \n", $fields);

    return sprintf(
      'ALTER TABLE %s %s;',
      $table,
      $fields
    );
  }

  /**
   * Removes a field
   *
   * @param *string $name Column name
   *
   * @return QueryInterface
   */
  public function removeField(string $name): QueryInterface
  {
    $this->removeFields[] = $name;
    return $this;
  }

  /**
   * Removes an index key
   *
   * @param *string $name Name of key
   *
   * @return QueryInterface
   */
  public function removeKey(string $name): QueryInterface
  {
    $this->removeKeys[] = $name;
    return $this;
  }

  /**
   * Removes a primary key
   *
   * @param *string $name Name of key
   *
   * @return QueryInterface
   */
  public function removePrimaryKey(string $name): QueryInterface
  {
    $this->removePrimaryKeys[] = $name;
    return $this;
  }

  /**
   * Removes a unique key
   *
   * @param *string $name Name of key
   *
   * @return QueryInterface
   */
  public function removeUniqueKey(string $name): QueryInterface
  {
    $this->removeUniqueKeys[] = $name;
    return $this;
  }
}
