<?php

/**
 *
 * @package Phpunit
 * @subpackage Fixture
 *
 * @author Maksim Kotlyar <mkotlar@ukr.net>
 *
 */
class sfBasePhpunitFixtureAggregator implements sfPhpunitFixtureAggregator
{
  public function getPackageFixtureDir()
  {
    return dirname($this->getOwnFixtureDir());
  }

  public function getOwnFixtureDir()
  {
    throw new Exception('Not implemented');
  }

  public function getCommonFixtureDir()
  {
    return sfConfig::get('sf_test_dir').'/phpunit/fixtures/common';
  }

  public function getSymfonyFixtureDir()
  {
    return sfConfig::get('sf_data_dir').'/fixtures';
  }
}