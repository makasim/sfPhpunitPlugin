<?php

class sfPhpunitDoSnapshotsTask extends sfBaseTask
{
  protected $fixture;

  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'test'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name'),
      // add your own options here
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'do-snapshots';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [phpunit:do-snapshots|INFO] task does things.
Call it with:

  [php symfony phpunit:do-snapshots|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    require_once sfConfig::get('sf_phpunit_dir').'/fixtures/sfPhpunitDoSnapshotContainer.php';

    $container = new sfPhpunitDoSnapshotContainer($this->dispatcher, $this->formatter);
    $container->doSnapshots();
  }
}