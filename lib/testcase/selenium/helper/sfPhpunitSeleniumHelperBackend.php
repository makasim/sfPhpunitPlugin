<?php

/**
 *
 * @package    sfPhpunitPlugin
 * @subpackage testcase
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitSeleniumHelperBackend extends sfBasePhpunitSeleniumHelper
{
  public function loginAdmin()
  {
    $this->login('admin', 'test');
  }

  public function login($username, $password)
  {    
  	$this->open();
    $this->_testCase->type("signin_username", $username);
    $this->_testCase->type("signin_password", $password);
    $this->_testCase->clickAndWait("css=form input[type='submit']");
  }

  public function logout()
  {
  	$this->open();
    $this->_testCase->clickAndWait("menu-signout");
  }

  public function reset()
  {
    if($this->_testCase->isElementPresent('menu-signout')) {
      $this->logout();
    }
  }

  public function clickAtTopMenu($clickAt)
  {
    $this->_testCase->clickAndWait($clickAt);
  }

  public function clickAtEditItem($itemId)
  {
    $this->_testCase->clickAndWait("//tr[{$itemId}]//li[@class='sf_admin_action_edit']/a");
  }

  public function clickAtDeleteItem($itemId)
  {
    //symfony confiramtion is failed in this case.
    $this->_testCase->click("//tr[{$itemId}]//li[@class='sf_admin_action_delete']/a");

    sleep(1);
    $this->_testCase->assertTrue(
      (bool)preg_match('/^Are you sure[\s\S]$/', 
       $this->_testCase->getConfirmation()));
    $this->_testCase->waitForPageToLoad();
  }

  public function clickAtNewItem()
  {
    $this->_testCase->clickAndWait("link=New");
  }

  public function clickAtBackToList()
  {
    $this->_testCase->clickAndWait("link=Back to list");
  }

  public function clickAtSaveForm()
  {
    $this->_testCase->clickAndWait("//input[@value='Save']");
  }

  public function assertFormNotSaved()
  {
    $this->_testCase->assertTextPresent('The item has not been saved due to some errors.');
  }

  public function assertFormSaved()
  {
    $this->_testCase->assertTextPresent('regexp:The item was (created|updated) successfully.');
  }

  public function assertFormFieldError($fileName, $errorText)
  {
    $this->_testCase->assertElementContainsText("css=.sf_admin_form_field_{$fileName}", $errorText);
  }
  
  public function assertFormGlobalError($errorText)
  {
    $this->_testCase->assertElementContainsText("css=ul.error_list li", $errorText);
  }

  public function assertItemFieldContains($itemId, $fieldName, $text, $fieldType = 'text')
  {
    $this->_testCase->assertElementContainsText(
      "//tbody/tr[{$itemId}]/td[@class='sf_admin_{$fieldType} sf_admin_list_td_{$fieldName}']",
      $text);
  }

  public function assertItemDeleted()
  {
    $this->_testCase->assertTextPresent("The item was deleted successfully.");
  }
  
  public function typeTinymce($frameId, $text)
  {
    $this->_testCase->selectFrame($frameId);
    $this->_testCase->focus('tinymce');
    $this->_testCase->type('tinymce', $text);
    $this->_testCase->selectFrame('relative=parent');
  }
  

  public function open()
  {
    $this->_testCase->open("/backend_test-selenium.php");
  }  
}