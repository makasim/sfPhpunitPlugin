<?php

class sfPhpunitPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $configFiles = $this->configuration->getConfigPaths('config/phpunit.yml');
    $config = sfDefineEnvironmentConfigHandler::getConfiguration($configFiles);
    
    foreach ($config as $name => $value) {
      sfConfig::set("sf_phpunit_{$name}", $value);  
    }
     
    $this->_getProjectConfiguration()->getEventDispatcher()->connect(
      'plugin.post_install',
      array($this, 'postInstall'));
      
    $this->loadFramework();
  }
  
  /**
   * @return sfProjectConfiguration
   */
  protected function _getProjectConfiguration()
  {
    return $this->configuration;
  }
  
  /**
   * Listen for event: command.post_command
   * 
   * @param sfEvent $event
   */
  public function postInstall(sfEvent $event) 
  {    
    $initTask = new sfPhpunitInitTask(
      $this->_getProjectConfiguration()->getEventDispatcher(), 
      new sfAnsiColorFormatter());
      
    $initTask->run();
  }
  
  /**
   * 
   * The scripts tries to include Phpunit Framework files, by default it 
   * 
   * defined to look for Phpunit 3.4 as a PEAR package
   * 
   * you can find standrat autload files in PLUGIN/config/autload*.php fiels
   * 
   * and check the config comes with a plugin.
   * 
   */
  protected function loadFramework() 
  {
    $options = sfConfig::get('sf_phpunit_framework');

    include $options['autoload_script'];
  }
}