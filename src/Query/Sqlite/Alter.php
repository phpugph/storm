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
   * @param *string $name     Column name
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
   * @param *string $name  Name of key
   * @param *string $table Name of key
   * @param *string $key   Name of key
   *
   * @return QueryInterface
   */
  public function addForeignKey(string $name, string $table, string $key): QueryInterface
  {
    $this->addKeys[$name] = [$table, $key];
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
    $this->addUniqueKeys[] = '"'.$name.'"';
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
   * @return string
   */
  public function getQuery(): string
  {
    $fields = [];
    $table = '"'.$this->table.'"';

    foreach ($this->removeFields as $name) {
      $fields[] = 'DROP "'.$name.'"';
    }

    foreach ($this->addFields as $name => $attr) {
      $field = ['ADD "'.$name.'"'];
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

      $fields[] = implode(' ', $field);
    }

    foreach ($this->changeFields as $name => $attr) {
      $field = ['CHANGE "'.$name.'"  "'.$name.'"'];

      if (isset($attr['name'])) {
        $field = ['CHANGE "'.$name.'"  "'.$attr['name'].'"'];
      }

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

      $fields[] = implode(' ', $field);
    }

    foreach ($this->removeKeys as $key) {
      $fields[] = 'DROP FOREIGN KEY "'.$key.'"';
    }

    foreach ($this->addKeys as $key => $value) {
      $fields[] = 'ADD FOREIGN KEY "'. $key .'" REFERENCES '.$value[0].'('.$value[1].')';
    }

    foreach ($this->removeUniqueKeys as $key) {
      $fields[] = 'DROP UNIQUE "'.$key.'"';
    }

    if (!empty($this->addUniqueKeys)) {
      $fields[] = 'ADD UNIQUE ('.implode(', ', $this->addUniqueKeys).')';
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
   * @param *string $name Name of field
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
  public function removeForeignKey(string $name): QueryInterface
  {
    $this->removeKeys[] = $name;
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
