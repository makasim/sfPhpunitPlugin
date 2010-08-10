<?php

class sfPhpunitDoctrineBaseData extends Doctrine_Data_Import
{
  protected $_snapshots = array();
  
  public function cleanObjects()
  {
    $this->_importedObjects = array();
    $this->_rows = array();
  }
  
  public function doSnapshot($name)
  {
    self::$_snapshots[$name]['importedObjects'] = $this->_importedObjects;
    self::$_snapshots[$name]['rows'] = $this->_rows;
  }
  
  public function loadSnapshot($name)
  {   
    $this->object_references = self::$_snapshots[$name];
  }
}