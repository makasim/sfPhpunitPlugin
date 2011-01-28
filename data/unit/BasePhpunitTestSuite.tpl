<?php

class {className} extends sfBasePhpunitTestSuite
  implements sfPhpunitContextInitilizerInterface
{
  /**
   * Dev hook for custom "setUp" stuff
   */
  protected function _start()
  {
    $this->_initFilters();
  }

  /**
   * Dev hook for custom "tearDown" stuff
   */
  protected function _end()
  {
  }

  protected function _initFilters()
  {
    $filters = sfConfig::get('sf_phpunit_filter', array());
    foreach ($filters as $filter) {

      if (version_compare(PHPUnit_Runner_Version::id(), '3.5') >= 0) {
        PHP_CodeCoverage_Filter::getInstance()->addDirectoryToBlacklist($filter['path']);
      } else {
        PHPUnit_Util_Filter::addDirectoryToFilter($filter['path'], $filter['ext']);
      }

    }

  }

  public function getApplication()
  {
    return '{application}';
  }
}