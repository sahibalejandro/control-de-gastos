<?php
class BaseController extends QuarkController
{
  /**
   * Page title used in view "layout/header.php"
   */
  protected $page_title;
  
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultAccessLevel(1);
  }
  
  /**
   * Create and return a PHPMailer object pre-configured to send email.
   * @param string $subject EMail subject
   * @return PHPMailer
   */
  protected function getPHPMailer($subject)
  {
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
      $this->renderView('layout/signup.php');
    }
  }
}
