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
 * Generates create table query string syntax
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Create extends AbstractQuery implements QueryInterface
{
  /**
   * @var array $fields List of fields
   */
  protected $fields = [];

  /**
   * @var array $primaryKeys List of primary keys
   */
  protected $primaryKeys = [];

  /**
   * @var array $oids Whether to use OIDs
   */
  protected $oids = false;

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
   * @return QueryCreate
   */
  public function addField($name, array $attributes)
  {
    $this->fields[$name] = $attributes;
    return $this;
  }

  /**
   * Adds a primary key
   *
   * @param *string $name Name of key
   *
   * @return QueryCreate
   */
  public function addPrimaryKey($name)
  {
    $this->primaryKeys[] = $name;
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

    $oids = $this->oids ? 'WITH OIDS': null;
    $fields = !empty($fields) ? implode(', ', $fields) : '';
    $primary = !empty($this->primaryKeys) ?
      ', PRIMARY KEY ("'.implode('", ""', $this->primaryKeys).'")' :
      '';

    return sprintf('CREATE TABLE %s (%s%s) %s;', $table, $fields, $primary, $oids);
  }

  /**
   * Sets a list of fields to the table
   *
   * @param array $fields List of fields
   *
   * @return QueryCreate
   */
  public function setFields(array $fields)
  {
    $this->fields = $fields;
    return $this;
  }

  /**
   * Sets a list of primary keys to the table
   *
   * @param *array $primaryKeys List of primary keys
   *
   * @return QueryCreate
   */
  public function setPrimaryKeys(array $primaryKeys)
  {
    $this->primaryKeys = $primaryKeys;
    return $this;
  }

  /**
   * Specifying if query should add the OIDs as columns
   *
   * @param bool $oids true or false
   *
   * @return QueryCreate
   */
  public function withOids($oids)
  {
    $this->oids = $oids;
    return $this;
  }
}
