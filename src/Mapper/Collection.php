<?php //-->
/**
 * This file is part of the Storm PHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Storm\Mapper;

use UGComponents\Data\Model\ModelInterface;
use UGComponents\Data\Collection\CollectionInterface;

use UGComponents\Data\Model as DataModel;
use UGComponents\Data\Collection as DataCollection;

use Storm\Engine\EngineInterface;

/**
 * Sql Collection handler
 *
 * @vendor   PHPUGPH
 * @package  Storm
 * @standard PSR-2
 */
class Collection extends DataCollection
{
  /**
   * @var EngineInterface $database = null The database resource
   */
  protected $database = null;

  /**
   * @var ?string $table Default table name
   */
  protected $table = null;

  /**
   * Returns the entire data
   *
   * @param array $row
   *
   * @return Model
   */
  public function getModel(array $row = []): ModelInterface
  {
    $model = $this->resolve(Model::class, $row);

    if (!is_null($this->database)) {
      $model->setDatabase($this->database);
    }

    if (!is_null($this->table)) {
      $model->setTable($this->table);
    }

    return $model;
  }

  /**
   * Sets the default database
   *
   * @param *EngineInterface $database Database object instance
   *
   * @return Collection
   */
  public function setDatabase(EngineInterface $database): Collection
  {
    $this->database = $database;

    //for each row
    foreach ($this->data as $row) {
      if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
        continue;
      }

      //let the row handle this
      $row->setDatabase($database);
    }

    return $this;
  }

  /**
   * Sets the default database
   *
   * @param string $table The name of the table
   *
   * @return Collection
   */
  public function setTable(string $table): Collection
  {
    $this->table = $table;

    //for each row
    foreach ($this->data as $row) {
      if (!is_object($row) || !method_exists($row, __FUNCTION__)) {
        continue;
      }

      //let the row handle this
      $row->setTable($table);
    }

    return $this;
  }
}
