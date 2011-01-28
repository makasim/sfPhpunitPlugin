<?php

class sfPhpunitDoctrineBaseData extends Doctrine_Data_Import
  implements Serializable
{
  protected static $_snapshots = array();

  protected $_importedObjectsIds = array();

  /**
   * Do the importing of the data parsed from the fixtures
   *
   * @return void
   */
  public function doImport($append = false)
  {
    $this->_rows = array();

    $result = parent::doImport($append);

    $this->_buildObjectsIds();

    return $result;
  }

  protected function _buildObjectsIds()
  {
    foreach ($this->_importedObjects as $key => $obj) {
      list(,$id) = explode(') ', $key, 2);
      $this->_importedObjectsIds[get_class($obj).'_'.$id] = $obj->identifier();
    }
  }

  public function cleanObjects()
  {
    $this->_importedObjects = array();
    $this->_rows = array();

    Doctrine_Manager::getInstance()->getCurrentConnection()->clear();
  }

  public function doSnapshot($name)
  {
    self::$_snapshots[$name]['importedObjects'] = $this->_importedObjects;
    self::$_snapshots[$name]['rows'] = $this->_rows;
  }

  public function loadSnapshot($name)
  {
    $this->_importedObjects = self::$_snapshots[$name]['importedObjects'];
    $this->_rows = self::$_snapshots[$name]['rows'];
  }

  public function cleanSnapshots()
  {
    $this->_importedObjects = array();
    $this->_rows = array();
  }

  public function serialize()
  {
    return serialize($this->_importedObjectsIds);
  }

  /**
   * @param serialized
   */
  public function unserialize($serialized)
  {
    $this->_importedObjectsIds = unserialize($serialized);
  }
}