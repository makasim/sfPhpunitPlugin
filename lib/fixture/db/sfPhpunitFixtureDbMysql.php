<?php

/**
 * 
 * @package sfPhpunitPlugin
 * @subpackage fixture 
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class sfPhpunitFixtureDbMysql extends sfPhpunitFixtureDb
{
  protected function getTables()
  {
    return $this->query("SHOW TABLES");
  }
  
  protected function disableConstraints()
  {
    $this->exec("SET FOREIGN_KEY_CHECKS = 0;");
    
    return $this;
  }
  
  protected function enableConstraints()
  {
    $this->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    return $this;
  }
}