<?php
class BaseController extends QuarkController
{
  /**
   * Page title used in view "layout/header.php"
   * Use setPageTitle() to set value
   */
  protected $page_title;
  
  /**
   * Data from signed user
   */
  protected $UserData;
  
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultAccessLevel(1);
    
    // Bind user data if exists
    if($this->QuarkSess->getAccessLevel() > 0){
      $this->UserData = $this->QuarkSess->get('UserData');
    }
  }
  
  /**
   * Set page title for view "layout/header.php"
   */
  protected function setPageTitle($page_title)
  {
    $this->page_title = $page_title;
    return $this;
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
      $auth_url    = null;
      $twitter_err = false;
      $TwitterAPI  = $this->getTwitterAPI();
      
      try {
        $auth_url = $TwitterAPI->getAuthenticationURL(
          $this->QuarkURL->getURL('home/check-authentication')
        );
      } catch (TwitterAPIException $e) {
        $twitter_err = $e->getMessage();
        Quark::log($twitter_err);
      }
      
      $this->renderView('layout/signup.php', array(
        'auth_url'    => $auth_url,
        'twitter_err' => $twitter_err
      ));
    }
  }
  
  /**
   * Returns the TwitterAPI object prepared with your own app's token
   */
  protected function getTwitterAPI()
  {
    return new TwitterAPI(
      '2NzGW2zJ0cJjBEuAUAjuIg',
      'b1MkQf72L5HIW39AVEskisbalHlOyyao3WYe8fPbLY'
    );
  }
}
