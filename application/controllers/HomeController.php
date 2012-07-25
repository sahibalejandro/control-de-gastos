<?php
class HomeController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
    
    // Set public access to methods
    $this->setActionsAccessLevel(array(
      'ajaxSignup' => 0,
      'ajaxLogin'  => 0,
      'recoverPassword'     => 0,
      'ajaxRecoverPassword' => 0,
    ));
  }
  
  public function index()
  {
  }
  
  /**
   * Display form for recover user password, this method is called by a route, see
   * config file.
   */
  public function recoverPassword()
  {
    $this->setPageTitle('Recuperar contraseña')->renderView();
  }
  
  /**
   * Search the registered email, if found generates a new password and send it
   * to user via email.
   */
  public function ajaxRecoverPassword()
  {
    // Trim email to avoid stupid finger errors.
    $_POST['email'] = trim($_POST['email']);
    
    // Search for registered email, if not found return error message, else generate
    // new password and send it to user via email.
    $User = UserORM::query()->findOne()->where(array('email' => $_POST['email']))
      ->puff();
      
    if( !$User ){
      $this->setAjaxResponse(null
        , 'Dirección de correo electrónico no registrada.', true);
    } else {
      try{
        // Update password
        $passwd = uniqid();
        $User->passwd = new QuarkSQLExpression('MD5(:passwd)'
          , array(':passwd' => $passwd));
        $User->save();
        
        // Send email message
        $Mail = $this->getPHPMailer('Recuperar contraseña');
        $Mail->AddAddress($_POST['email'], $User->name);
        $Mail->MsgHTML($this->renderView('home/recover-password-email.php'
          , array('name' => $User->name, 'passwd' => $passwd), true));
        $Mail->Send();
        
        // Return message through AJAX
        $this->setAjaxResponse(null
          , 'Se ha enviado tu nueva contraseña a tu correo electrónico');
      } catch(QuarkORMException $e) {
        // Fail to save new password!
        $this->setAjaxResponse(null, 'No se pudo generar la nueva contraseña', true);
      }
    }
  }
  
  /**
   * Get data from POST to sign in a user through AJAX
   */
  public function ajaxLogin()
  {
    try {
      $UserData = UserORM::query()
        ->selectOne('id','email','name')
        ->where(array(
          'email' => $_POST['login_email'],
          'passwd' => new QuarkSQLExpression('MD5(:passwd)', array(
            ':passwd' => $_POST['login_passwd']
          ))
        ))->puff();
      
      if($UserData === false){
        // User not found, return error message
        $this->setAjaxResponse(null
          , 'Correo electrónico y/o contraseña incorrectos.'
          , true);
      } else {
        // Set session access level and save user data, return data is not needed.
        $this->QuarkSess->setAccessLevel(1);
        $this->QuarkSess->set('UserData', $UserData);
        
        // Save cookie if user wants to be remembered
        if(isset($_POST['login_cookie']) && $_POST['login_cookie'] == 1){
          $this->QuarkSess->saveCookie();
        }
      }
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null
        , 'No se pudo iniciar la sesión, intenta más tarde.'
        , true);
    }
  }
  
  /**
   * Signup a new user and send welcome email.
   */
  public function ajaxSignup()
  {
    // trim POST data
    $_POST = array_map('trim', $_POST);
    
    // Verfy if user accepted the terms and conditions.
    if(!isset($_POST['accept_terms']) || $_POST['accept_terms'] != 1){
      $this->setAjaxResponse(null
        , 'Debes aceptar los términos y condiciones.', true);
    }
    
    // Validate name, email and password
    else if( empty($_POST['signup_name'])
      || empty($_POST['signup_email'])
      || empty($_POST['signup_passwd'])
      || strpos($_POST['signup_email'], '@') === false
      || strlen($_POST['signup_passwd']) < 8
      || strlen($_POST['signup_passwd']) > 20 ){
      $this->setAjaxResponse(null
        , 'Nombre, correo electrónico y/o contraseña inválidos.', true);
    
    // Search for already registered email
    } elseif(UserORM::query()->count()->where(array(
        'email' => $_POST['signup_email']
      ))->puff() > 0) {
      
      $this->setAjaxResponse(null
        , 'El correo electrónico '. $_POST['signup_email']. ' ya ha sido registrado.'
        , true);
    } else {
      try {
        // Try to insert user data in database
        $User = new UserORM();
        $User->email = $_POST['signup_email'];
        $User->name = $_POST['signup_name'];
        $User->passwd = new QuarkSQLExpression('MD5(:passwd)'
          , array(':passwd' => $_POST['signup_passwd']));
        $User->save();
        
        // Try to send welcome email to user
        $Mail = $this->getPHPMailer('Solicitud de registro.');
        $Mail->AddAddress($_POST['signup_email'], $_POST['signup_name']);
        $Mail->MsgHTML( $this->renderView('layout/signup-email.php', array(
          'name' => $_POST['signup_name'],
          'email' => $_POST['signup_email'],
          'passwd' => $_POST['signup_passwd'],
        ), true) );
        if( $Mail->Send() ){
          $this->setAjaxResponse(null,
            'El registro se ha realizado, ya puedes iniciar sesión.');
        } else {
          $this->setAjaxResponse(null, $Mail->ErrorInfo, true);
        }

      } catch (QuarkORMException $e) {
        // Fail to insert user data into database!
        $this->setAjaxResponse(null, 'No se pudo realizar el registro.', true);
      }
    }
  }
}
