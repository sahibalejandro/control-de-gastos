<?php
class SpendControlController extends QuarkController
{
  public function __quarkAccessDenied()
  {
    if(QUARK_AJAX){
      $this->setAjaxAccessDenied();
    } else {
      $this->renderView('home/login.php');
    }
  }
}
