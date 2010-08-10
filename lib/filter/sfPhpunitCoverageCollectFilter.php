<?php

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitCoverageCollectFilter extends sfFilter
{
  public function execute($filterChain)
  {
    if (!$this->getOption('enabled') || !extension_loaded('xdebug') || !$this->isFirstCall()) {
      $filterChain->execute();
      return;
    }
    // TODO
    if (sfConfig::get('sf_environment') != 'test') {
      $filterChain->execute();
      return;
    }

    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE); 

    $filterChain->execute();
                  
    $this->collect(xdebug_get_code_coverage());
    xdebug_stop_code_coverage();
  }
  
  protected function collect(array $data)
  {
    $logDir = $this->getOption('dir');
    
    $this->getFilesystem()->mkdirs($logDir);
    
    $logFile = $logDir.'/'.date('Y-m-d_H:i:s').'_'.rand(1000, 10000); 
    
    file_put_contents($logFile, serialize($data));
  }
  
  /**
   * 
   * @return sfFilesystem
   */
  protected function getFilesystem()
  {
    return new sfFilesystem(
      sfContext::getInstance()->getEventDispatcher(), 
      new sfFormatter());
  }
  
  protected function getOption($name)
  {
    $options = sfConfig::get('sf_phpunit_coverage');
    
    return isset($options[$name]) ? $options[$name] : null;
  }
}