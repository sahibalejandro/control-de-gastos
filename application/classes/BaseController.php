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
   * Create and return a PHPMailer object pre-configured to send email.
   * 
   * @deprecated
   * @param string $subject EMail subject
   * @return PHPMailer
   */
  protected function getPHPMailer($subject)
  {
    return null;
    
    require_once QUARK_APP_PATH . '/includes/phpmailer/class.phpmailer.php';
    /*
     * TODO: Change SMTP values to connect to production server.
     */
    $Mail = new PHPMailer();
    $Mail->IsSMTP();
    $Mail->Host     = 'mail.vivalaweba.com';
    $Mail->SMTPAuth = true;
    $Mail->Port     = 26;
    $Mail->Username = 'admin+vivalaweba.com';
    $Mail->Password = '^7EN9J;Zfdp8L_z4Gz';
    $Mail->Subject  = $subject;
    $Mail->AltBody  = 'Para ver este mensaje usa un lector de correo compatible con HTML';
    $Mail->SetFrom('admin@vivalaweba.com', 'Control de gastos');
    $Mail->AddReplyTo('admin@vivalaweba.com', 'Control de gastos');
    return $Mail;
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
      'Fcqdt9RwHUxMgI3Yk4kxqg',
      'LGfZGv9G8FV8u1iCUkhcLOhezp5Pilf5LROJIdwPhI'
    );
  }
}
