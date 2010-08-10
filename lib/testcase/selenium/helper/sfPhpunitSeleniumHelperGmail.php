<?php

/**
 *
 * @package    sfPhpunitPlugin
 * @subpackage testcase
 * 
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitSeleniumHelperGmail extends sfBasePhpunitSeleniumHelper
{
  /**
   * @return GmailHelper
   */
  public function open()
  {
    //wait for mail to be delivered
    sleep(10);
    
    $this->_testCase->openAndWait('http://gmail.com');
    
    return $this;
  }
  
  /**
   * @return GmailHelper
   */
  public function loginRegistredUser()
  {
    $registredUserEmail = $this->_testCase->
      frontendHelper()->getRegisteredUser()->getProfile()->getEmail();
    
    $this->_testCase->type("Email", $registredUserEmail);
    $this->_testCase->type("Passwd", "asdfFDSA123#");
    $this->_testCase->clickAndWait("signIn");
    
    return $this;
  }
  
  /**
   * @return GmailHelper
   */
  public function loginNewUser()
  {
    $this->_testCase->type("Email", "newuser.jjthreads");
    $this->_testCase->type("Passwd", "asdfFDSA123#");
    $this->_testCase->clickAndWait("signIn");
    
    return $this;
  }
  
  /**
   * @return GmailHelper
   */
  public function assertNewMail($from, $subject)
  {
    $this->_testCase->assertTextPresent($from);
    $this->_testCase->assertElementContainsText("//tr[@bgcolor='#ffffff']//td[3]//b[1]", $subject);
    
    return $this;
  }
  
  /**
   * @return GmailHelper
   */
  public function clickAtMailWithSubject($subject)
  {
    $this->_testCase->clickAndWait("link={$subject}");
    
    return $this;
  }
}