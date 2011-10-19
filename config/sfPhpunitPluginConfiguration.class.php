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
}