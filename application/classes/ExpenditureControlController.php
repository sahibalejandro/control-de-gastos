<?php
class ExpenditureControlController extends QuarkController
{
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultAccessLevel(1);
  }
  
  /**
   * Renders the login page or send an "access denied" response through
   * AJAX if needed.
   */
  public function __quarkAccessDenied()
  {
    if(QUARK_AJAX){
      $this->setAjaxAccessDenied();
    } else {
      $this->renderView('layout/login.php');
    }
  }
}
