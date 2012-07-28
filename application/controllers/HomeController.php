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
  
  /**
   * Show the interface to manage the user's spending accounts.
   */
  public function index()
  {
    // Render view with user accounts
    $this->setPageTitle('Mis cuentas')->renderView(null, array(
      'user_accounts' => AccountORM::getUserAccounts($this->UserData->id)
    ));
  }

  /**
   * Delete the movement specified by $_POST['movement_id']
   */
  public function ajaxDeleteMovement()
  {
    try {
      MovementORM::query()->delete()
        ->where('`id` = :movement_id AND `users_id` = :user_id', array(
          ':movement_id' => $_POST['movement_id'],
          ':user_id' => $this->UserData->id
        ))
        ->puff();
      // Send total amounts updated
      $this->setAjaxResponse(array(
        'total_amounts' => $this->getUserTotalAmounts()
      ));
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo borrar el movimiento', true);
    }
  }

  /**
   * Change the type of the movement specified by $_POST['movement_id']
   * Sends the new type via AJAX
   */  
  public function ajaxChangeMovementType()
  {
    try {
      $MovementORM = MovementORM::query()->findOne()
        ->where('`id` = :movement_id AND `users_id` = :user_id', array(
          ':movement_id' => $_POST['movement_id'],
          ':user_id' => $this->UserData->id))
        ->puff();
      if(!$MovementORM){
        $this->setAjaxResponse(null, 'Movimiento no encontrado', true);
      } else {
        $MovementORM->type = $MovementORM->type == 1 ? 0 : 1;
        $MovementORM->save();
        
        // Send response
        $this->setAjaxResponse(array(
          'type' => $MovementORM->type,
          // Add total user amounts to update DOM
          'total_amounts' => $this->getUserTotalAmounts()
        ));
      }
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo cambiar el tipo de movimiento', true);
    }
  }
  
  /**
   * Loads movements from database to show on a movements list account specified
   * by $_POST['account_id'] and user ID
   */
  public function ajaxLoadMoreMovements()
  {
    try {
      $loaded_ids     = array();
      $max_timestamp  = $_POST['max_timestamp'];
      $movements_html = '';
      
      // Retrieve the movements
      $movements = MovementORM::getSince($this->UserData->id
        , $_POST['account_id']
        , $_POST['max_timestamp']);

      if(count($movements) > 0){
        // Extract the movement's IDs and build render
        foreach($movements as $MovementORM){
          $loaded_ids[] = $MovementORM->id;
          $movements_html .= $this->renderView('home/movement.php'
            , array('MovementORM' => $MovementORM), true);
        }
        // Get timestamp from last $MovementORM
        $max_timestamp = strtotime($MovementORM->date);
      }
      
      // Send ajax response
      $this->setAjaxResponse(array(
        'loaded_ids'     => $loaded_ids,
        'max_timestamp'  => $max_timestamp,
        'movements_html' => $movements_html
      ));
      
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo cargar más movimientos', true);
    }
  }
  
  /**
   * Add new account to signed user
   */
  public function ajaxAddAccount()
  {
    $_POST['account_name'] = trim($_POST['account_name']);
    settype($_POST['account_init_amount'], 'float');
    if($_POST['account_name'] == ''
      || $_POST['account_init_amount'] < 1){
      $this->setAjaxResponse(null
        , 'Especifica el nombre de la cuenta y un monto inicial', true);
    } else {
      try {
        
        // Create new account
        $AccountORM = new AccountORM();
        $AccountORM->name     = $_POST['account_name'];
        $AccountORM->color    = $_POST['account_color'];
        $AccountORM->users_id = $this->UserData->id;
        $AccountORM->save();
        
        // Add initial movement to new account
        $MovementORM = new MovementORM();
        $MovementORM->amount = $_POST['account_init_amount'];
        $MovementORM->setParent($AccountORM);
        $MovementORM->users_id = $this->UserData->id;
        $MovementORM->type = 1;
        $MovementORM->concept = 'Monto inicial';
        $MovementORM->save();
        
        // Get account HTML render
        $account_html = $this->renderView('home/account.php'
          , array('AccountORM' => $AccountORM), true);
        
        // Return HTML render and message
        $this->setAjaxResponse(array(
          'account_html' => $account_html,
          // Add total amounts to update DOM
          'total_amounts' => $this->getUserTotalAmounts()
          ), 'Cuenta "'.$_POST['account_name'].'" creada.');
        
      } catch (QuarkORMException $e) {
        $this->setAjaxResponse(null, 'No se pudo agregar la nueva cuenta', true);
      }
    }
  }
  
  protected function getUserTotalAmounts()
  {
    return array(
      'total' => 0,
      'payments' => 0,
      'available' => 0
    );
  }
  
  /**
   * Close user session, this method is called by a route, see config file.
   */
  public function logout()
  {
    $this->QuarkSess->kill();
    $this->__quarkAccessDenied();
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
