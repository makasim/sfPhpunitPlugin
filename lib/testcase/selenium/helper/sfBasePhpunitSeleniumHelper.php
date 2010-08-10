<?php

/**
 *
 * @package    sfPhpunitPlugin
 * @subpackage testcase
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
abstract class sfBasePhpunitSeleniumHelper
{ 
  /**
   * 
   * @var PHPUnit_Extensions_SeleniumTestCase
   */
  protected $_testCase;
  
  public function __construct(PHPUnit_Extensions_SeleniumTestCase $testCase)
  {
    $this->_testCase = $testCase;
  }
}