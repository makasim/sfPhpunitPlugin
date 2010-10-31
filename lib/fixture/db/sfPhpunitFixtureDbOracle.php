<?php

/**
 * 
 * @package sfPhpunitPlugin
 * @subpackage fixture 
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class sfPhpunitFixtureDbOracle extends sfPhpunitFixtureDb
{
  public function clean()
  {
    $this->beginTransaction();
    
    parent::clean();
    
    $this->commit();

    return $this;
  }
  
  public function loadSnapshot()
  {
    $this->beginTransaction();
    
    parent::loadSnapshot();
    
    $this->commit();

    return $this;
  }
  
  public function doSnapshot()
  {
    $this->beginTransaction();
    
    parent::doSnapshot();
    
    $this->commit();

    return $this;
  }
  
  public function cleanSnapshots()
  {
    $this->beginTransaction();
    
    parent::cleanSnapshots();
    
    $this->commit();

    return $this;
  }
  
  protected function getTables()
  {
    return $this->query("select table_name from user_tables");
  }
  
  protected function disableConstraints()
  {
    $queryStr = "SELECT 'alter table '||table_name||' disable constraint '||constraint_name||'' FROM user_constraints WHERE constraint_type = 'R'";
    $query = $this->query($queryStr);
    while($constraint = $query->fetchColumn()){
      $this->exec($constraint);
    }
    
    return $this;
  }
  
  protected function enableConstraints()
  {
    $queryStr = "SELECT 'alter table '||table_name||' enable constraint '||constraint_name||'' FROM user_constraints WHERE constraint_type = 'R'";
    $query = $this->query($queryStr);
    while($constraint = $query->fetchColumn()){
      $this->exec($constraint);
    }
    
    return $this;
  }
}