<?php

class sfPhpunitDoctrineData14 extends sfPhpunitDoctrineBaseData
{
  /**
   * (non-PHPdoc)
   * @see plugins/sfPhpunitPlugin/lib/fixture/data/sfPhpunitDataInterface#getObject($id, $class)
   */
  public function getObject($ymlObjectKey)
  {
    if (strpos($ymlObjectKey, '_') === false) {
      throw new Exception('The id should match the pattern {model}_{id} but you provide: `'.$ymlObjectKey.'`');
    }
    if (!isset($this->_importedObjectsIds[$ymlObjectKey])) {
      throw new Exception('The data object with given id `'.$ymlObjectKey.'` does not exist');
    }

    list($class, $objectKey) = explode('_', $ymlObjectKey, 2);
    $table = Doctrine_Core::getTable($class);
    $query = $table->createQuery();

    foreach ($this->_importedObjectsIds[$ymlObjectKey] as $field => $id) {
      $query->andWhere("$field = ?", $id);
    }

    if (!$obj = $query->fetchOne()) {
      throw new Exception('Something goes wrong. We have yml object identifier `'.$ymlObjectKey.'` but nothing related in DB');
    }

    return $obj;
  }
}