<?php

/**
 *
 * @package sfPhpunitPlugin
 * @subpackage fixture
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
abstract class sfPhpunitFixtureDb
{
  protected $_connection;

  public function __construct($connection)
  {
    $this->_connection = $connection;
  }

  public function __call($name, $args)
  {
    return call_user_func_array(array($this->_connection, $name), $args);
  }

  public function clean()
  {
    $this->disableConstraints();

    $query = $this->getTables();
    while($table = $query->fetchColumn()) {
      if (strpos($table, $this->getSnaphotTablePrefix()) !== false) continue;

      $this->exec("TRUNCATE TABLE {$table}");
    }

    $this->enableConstraints();

    return $this;
  }

  public function loadSnapshot($name)
  {
    $this->disableConstraints();

    $query = $this->getTables();
    while($table = $query->fetchColumn()) {
      if (strpos($table, $this->getSnaphotTablePrefix()) !== false) continue;

      $snapshop_table = "{$this->getSnaphotTablePrefix()}_{$name}__{$table}";
      $this->exec("TRUNCATE TABLE {$table}");
      $this->exec("INSERT INTO {$table} SELECT * FROM {$snapshop_table}");
    }

    $this->enableConstraints();
  }

  public function doSnapshot($name)
  {
    $query = $this->getTables();
    while($table = $query->fetchColumn()) {
      if (strpos($table, $this->getSnaphotTablePrefix()) !== false) continue;

      $snapshop_table = "{$this->getSnaphotTablePrefix()}_{$name}__{$table}";
      $this->exec("DROP TABLE IF EXISTS {$snapshop_table}");
      $this->exec("CREATE TABLE {$snapshop_table} SELECT * FROM {$table}");
    }

    return $this;
  }

  public function cleanSnapshots()
  {
    $query = $this->getTables();
    while($table = $query->fetchColumn()) {
      if (strpos($table, $this->getSnaphotTablePrefix()) === false) continue;

      $this->exec("DROP TABLE IF EXISTS {$table}");
    }

    return $this;
  }

  protected function getSnaphotTablePrefix()
  {
    return '_snapshot';
  }

  abstract protected function getTables();

  abstract protected function disableConstraints();

  abstract protected function enableConstraints();

  /**
   *
   * @param mixed $connection
   *
   * @return sfPhpunitFixtureDb
   */
  public static function factory($connection)
  {
    return $connection instanceof Doctrine_Adapter_Oracle ?
      new sfPhpunitFixtureDbOracle($connection) :
      new sfPhpunitFixtureDbMysql($connection);
  }
}