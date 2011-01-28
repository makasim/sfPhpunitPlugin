<?php

/**
 *
 * Class for managing doctrine fixtures.
 *
 * @package    sfPhpunitPlugin
 * @subpackage fixture
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitFixtureDoctrine extends sfPhpunitFixture
{
  /**
   *
   * @var array
   */
  protected $_options = array(
    'fixture_ext' => '.doctrine.yml',
    'snapshot-table-prefix' => '_snapshot');

  /**
   * (non-PHPdoc)
   * @see plugins/sfPhpunitPlugin/lib/fixture/sfPhpunitFixtureAbstract#load($file, $fixture_type, $clean_before)
   */
  public function load($file = null, $fixture_type = self::OWN)
  {
    $path = $this->getDir($fixture_type);
    $ext = $this->_getOption('fixture_ext');

    $files = $this->getFiles($file, $fixture_type);
    if (empty($files)) {
      $path = is_null($file) ?
      $this->getDir($fixture_type) : $this->getDir($fixture_type).'/'.$file.$this->_getExt();
      throw new Exception('There is nothing to load under the path '.$path);
    }

    foreach ($files as $file) {
      $this->_getDataLoader()->setFormat('yml');
      $this->_getDataLoader()->setDirectory($file);
      $this->_getDataLoader()->doImport(true);
    }

    return $this;
  }

  /**
   * (non-PHPdoc)
   * @see plugins/sfPhpunitPlugin/lib/fixture/sfPhpunitFixtureAbstract#get($file, $fixture_type)
   */
  public function get($id)
  {
    return $this->_getDataLoader()->getObject($id);
  }

  /**
   * (non-PHPdoc)
   * @see plugins/sfPhpunitPlugin/lib/fixture/sfPhpunitFixture::_pdo()
   */
  protected function _pdo()
  {
    return sfPhpunitFixtureDb::factory(
      Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh());
  }

  protected function _getDataLoader()
  {
    if (!$this->_data) {
      if (version_compare(SYMFONY_VERSION, '1.2.0', '>=') &&
        version_compare(SYMFONY_VERSION, '1.3.0', '<'))
      {

        $dataClass = 'sfPhpunitDoctrineData12';

      } else if (version_compare(SYMFONY_VERSION, '1.3.0', '>=') &&
        version_compare(SYMFONY_VERSION, '1.5.0', '<='))
      {

        $dataClass = 'sfPhpunitDoctrineData14';

      } else {
        throw new LogicException('The symfony version `'.SYMFONY_VERSION.'` is not supported for doctrine fixture');
      }

      $this->_data = new $dataClass();
    }

    return $this->_data;
  }
}