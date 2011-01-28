<?php

class phpunitDosnapshotsTask extends sfBaseTask
{
  protected $fixture;

  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'admin'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'test'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
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
    echo 'Init context: ';

    $this->fixture = $fixture = sfPhpunitFixture::build(new sfBasePhpunitFixtureDoctrineAggregator());

    $configuration = ProjectConfiguration::getApplicationConfiguration('admin', 'test', true);
    $databaseManager = new sfDatabaseManager($configuration);
    $connection = $databaseManager->getDatabase('doctrine')->getConnection();

    $fixture->clean()->loadCommon('diem_data');

    dm::createContext($configuration);

    echoln('OK');
    echo 'Clean snapshots: ';

    $fixture->cleanSnapshots();

    echoln('OK');
    echo 'Start `diem_data`: ';

    $fixture->clean()
      ->loadCommon('diem_data')
      ->doSnapshot('diem_data');

    echoln('OK');
    echo 'Start `users`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->doSnapshot('users');

    echoln('OK');
    echo 'Start `diem_data` and `users`: ';

    $fixture->clean()
      ->loadCommon('diem_data')
      ->loadCommon('users')
      ->doSnapshot('diem_data_users');

    echoln('OK');
    echo 'Start `users`: ';

    echo 'Start `users` and `mail_permissions`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('user_mail_permissions')
      ->doSnapshot('u_mp');

    echoln('OK');
    echo 'Start `users` and `category_details`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_with_detail')
      ->doSnapshot('u_category_with_detail');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_category`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_categories')
      ->doSnapshot('u_detail_cat_cat');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_customer`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_customers')
      ->doSnapshot('u_detail_cat_cus');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_intersts`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_interests')
      ->doSnapshot('u_detail_cat_int');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_kiosk`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_kiosks')
      ->doSnapshot('u_detail_cat_kio');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_objects`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_objects')
      ->doSnapshot('u_detail_cat_obj');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_partners`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_partners')
      ->doSnapshot('u_detail_cat_par');

    echoln('OK');
    echo 'Start `users` and `category_details` and `category_surveys`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('categories_detail')
      ->loadCommon('category_surveys')
      ->doSnapshot('u_detail_cat_sur');


    echoln('OK');
    echo 'Start `users` and `category_surveys`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('category_surveys')
      ->doSnapshot('u_cat_sur');

    echoln('OK');
    echo 'Start `users` and `category_objects`: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('category_objects')
      ->doSnapshot('u_cat_obj');

    echoln('OK');
    echo 'Start `category_details`: ';

    $fixture->clean()
      ->loadCommon('categories_detail')
      ->doSnapshot('cd');

    echoln('OK');
    echo 'Start `users` and all categories: ';

    $fixture->clean()
      ->loadCommon('users')
      ->loadCommon('category_objects')
      ->loadCommon('category_categories')
      ->loadCommon('category_customers')
      ->loadCommon('category_kiosks')
      ->loadCommon('category_partners')
      ->doSnapshot('u_all_cat');

    echoln('OK');
  }
}