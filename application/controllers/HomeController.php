<?php
class HomeController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
    // Set public access to methods
    $this->setActionsAccessLevel(array(
      'checkAuthentication' => 0
    ));
  }
  
  /**
   * Show the interface to manage the user's spending accounts.
   */
  public function index()
  {
    // Render view with user accounts
    $this->setPageTitle('Mis cuentas')->renderView(null, array(
      'total_amounts' => $this->getUserTotalAmounts(),
      'user_accounts' => AccountORM::getUserAccounts($this->UserData->id)
    ));
  }
  
  /**
   * Pay a payment creating a movement in an account specified
   */
  public function ajaxPayPayment()
  {
    // Sanitize data
    settype($_POST['payment_to_pay_id'], 'int');
    settype($_POST['pay_account_id'], 'int');
    
    try{
      // Check if payment and account belongs to signed user
      $PaymentORM = PaymentORM::query()->findOne()
        ->where(array(
          'id' => $_POST['payment_to_pay_id'],
          'users_id' => $this->UserData->id
        ))
        ->puff();
        
      $AccountORM = AccountORM::query()->findOne()
        ->where(array(
          'id' => $_POST['pay_account_id'],
          'users_id' => $this->UserData->id
        ))
        ->puff();
        
      if (!$PaymentORM || !$AccountORM) {
        $this->setAjaxResponse(null, 'El pago o la cuenta no existen', true);
      } else {
        // Create new movement in the account
        $MovementORM = new MovementORM();
        $MovementORM->users_id = $this->UserData->id;
        $MovementORM->accounts_id = $_POST['pay_account_id'];
        $MovementORM->amount = $PaymentORM->amount;
        $MovementORM->type = 0;
        $MovementORM->concept = 'Pago de: '.$PaymentORM->concept;
        $MovementORM->save();
        
        // Update the payment amount to 0 (cero)
        $PaymentORM->amount = 0;
        $PaymentORM->save();
        
        // Return the updated payment data, movement render and new total amounts
        $this->setAjaxResponse(array(
          // Total amounts data
          'total_amounts'  => $this->getUserTotalAmounts(),
          // Formatted account amount
          'account_amount' => '$'. number_format(
            $this->getAccountAmount($MovementORM->accounts_id),
            2
          ),
          // Payment data
          'payment'        => $PaymentORM->getArrayForAJAX(),
          // Movement render
          'movement_html'  => $this->renderView(
            'home/movement.php',
            array('MovementORM' => $MovementORM),
            true
          )
        ),
        'Pago realizado');
      }
        
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo realizar el pago', true);
    }
  }
  
  /**
   * Load user's accounts list (just "id" and "name" fields for now).
   */
  public function ajaxLoadAccountsList()
  {
    try {
      $this->setAjaxResponse(
        AccountORM::query()->select('id', 'name')
          ->where(array('users_id' => $this->UserData->id))
          ->order('name', 'asc')
          ->puff()
      );
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo cargar la lista de cuentas', true);
    }
  }
  
  /**
   * Delete a payment
   */
  public function ajaxDeletePayment()
  {
    try {
      $rows_count = PaymentORM::query()
        ->delete()
        ->where(array(
          'id' => $_POST['payment_id'],
          'users_id' => $this->UserData->id
        ))
        ->puff();
      
      if ($rows_count == 0) {
        $this->setAjaxResponse(null, 'No se borro ningún pago', true);
      } else {
        // Return total amounts to update client's GUI
        $this->setAjaxResponse(array(
          'total_amounts' => $this->getUserTotalAmounts()
        ), 'Pago eliminado');
      }
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo borrar el bago', true);
    }
  }
  
  /**
   * Save payment data from $_POST
   */
  public function ajaxSavePayment()
  {
    // Sanitize input data
    settype($_POST['id'], 'int');
    settype($_POST['amount'], 'float');
    
    try {
      // Create new payment or get existent payment
      if ($_POST['id'] == 0) {
        // Create new payment
        $Payment = new PaymentORM();
        $Payment->users_id = $this->UserData->id;
      } else {
        // Get existent payment
        $Payment = PaymentORM::query()->findOne()
        ->where(array(
          'id' => $_POST['id'],
          'users_id' => $this->UserData->id
        ))
        ->puff();
      }
      
      // Update payment data if exists.
      if ($Payment == false) {
        $this->setAjaxResponse(null, 'Pago no encontrado', true);
      } else {
        $Payment->amount = $_POST['amount'];
        $Payment->concept = trim($_POST['concept']);
        $Payment->save();
        
        // Return payment data and total amounts.
        $this->setAjaxResponse(
          array(
            'payment' => $Payment->getArrayForAJAX(),
            'total_amounts' => $this->getUserTotalAmounts()
          ),
          'Pago actualizado'
        );
      }
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo guardar el pago', true);
    }
  }
  
  /**
   * Get all user's payments and send it through ajax response
   */
  public function ajaxLoadPayments()
  {
    try {
      $payments = array();
      foreach (PaymentORM::query()->find()
        ->where(array('users_id' => $this->UserData->id))
        ->order('id', 'ASC')
        ->puff() as $Payment
      ) {
        $payments[] = $Payment->getArrayForAJAX();
      }
      
      $this->setAjaxResponse($payments);
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo obtener la lista de pagos', true);
    }
  }
  
  /**
   * Delete an account specified by the ID in $_POST['account_id']
   */
  public function ajaxDeleteAccount()
  {
    try {
      $rows_count = AccountORM::query()->delete()
        ->where(array(
          'users_id' => $this->UserData->id,
          'id' => $_POST['account_id']))
        ->puff();
      if ($rows_count == 0) {
        $this->setAjaxResponse(null, 'No se borró ningúna cuenta', true);
      } else {
        $this->setAjaxResponse(
          array('total_amounts' => $this->getUserTotalAmounts()),
          'La cuenta ha sido borrada');
      }
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo borrar la cuenta', true);
    }
  }

  /**
   * Insert or update a movement into database
   */
  public function ajaxSaveMovement()
  {
    $_POST['movement_concept'] = trim($_POST['movement_concept']);
    settype($_POST['movement_amount'], 'float');
    settype($_POST['movement_id'], 'int');
    
    if($_POST['movement_concept'] == '' || $_POST['movement_amount'] == 0){
      $this->setAjaxResponse(null, 'El formulario tiene datos inválidos', true);
    } else {
      try {
        if($_POST['movement_id'] == 0){
          $MovementORM = new MovementORM();
          $MovementORM->users_id    = $this->UserData->id;
          $MovementORM->accounts_id = $_POST['movement_account_id'];
        } else {
          // find movement to edit
          $MovementORM = MovementORM::query()->findOne()
            ->where('`id`=:movement_id AND `accounts_id`=:account_id', array(
              ':movement_id' => $_POST['movement_id'],
              ':account_id'  => $_POST['movement_account_id']
            ))
            ->puff();
        }
        
        if(!$MovementORM){
          $this->setAjaxResponse(null, 'Movimiento no encontrado', true);
        } else {
          // Update movement and save
          $MovementORM->amount = $_POST['movement_amount'];
          $MovementORM->concept = $_POST['movement_concept'];
          $MovementORM->type = $_POST['movement_type'];
          // Append time to the date
          $MovementORM->date = $_POST['movement_date'] . ' ' . date('H:i:s');
          $MovementORM->save();
          
          // Returns the movement's html render to update the DOM
          $this->setAjaxResponse(array(
            'movement_html' => $this->renderView('home/movement.php', array(
              'MovementORM' => $MovementORM
            ), true),
            // Total amounts for update DOM
            'total_amounts' => $this->getUserTotalAmounts(),
            // Account's new amount to update DOM
            'account_amount' => '$'. number_format(
              $this->getAccountAmount($_POST['movement_account_id']), 2)
          ));
        }
        
      } catch (QuarkORMException $e) {
        $this->setAjaxResponse(null, 'No se pudo guardar el movimiento', true);
      }
    }
  }

  /**
   * Delete the movement specified by $_POST['movement_id']
   */
  public function ajaxDeleteMovement()
  {
    try {
      $MovementORM = MovementORM::query()->findOne()
        ->where('`id` = :movement_id AND `users_id` = :user_id', array(
          ':movement_id' => $_POST['movement_id'],
          ':user_id' => $this->UserData->id
        ))
        ->puff();
      
      $MovementORM->delete();
      
      $this->setAjaxResponse(array(
        // Send total amounts updated
        'total_amounts' => $this->getUserTotalAmounts(),
        // Send account amount
        'account_id'     => $MovementORM->accounts_id,
        'account_amount' => '$'. number_format(
          $this->getAccountAmount($MovementORM->accounts_id),
          2
        )
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
          'total_amounts' => $this->getUserTotalAmounts(),
          // Add account amount to update DOM
          'account_id' => $MovementORM->accounts_id,
          'account_amount' => '$'. number_format(
            $this->getAccountAmount($MovementORM->accounts_id),
            2
          )
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
   * Save an account into database, can be a new account or existing account
   * if is an existing account then 'account_init_amount' is ignored.
   */
  public function ajaxSaveAccount()
  {
    $_POST['account_name'] = trim($_POST['account_name']);
    settype($_POST['account_init_amount'], 'float');
    
    if ($_POST['account_name'] == '') {
      $this->setAjaxResponse(null, 'Especifica el nombre de la cuenta', true);
    } elseif ($_POST['account_id'] == 0 && $_POST['account_init_amount'] < 1) {
      $this->setAjaxResponse(null, 'Especifica un monto incial válido', true);
    } else {
      try {
        
        if ($_POST['account_id'] == 0) {
          // Create new account and bind to the actual user
          $AccountORM = new AccountORM();
          $AccountORM->users_id = $this->UserData->id;
        } else {
          // Get account to edit
          $AccountORM = AccountORM::query()->findOne()
            ->where(array(
              'users_id' => $this->UserData->id,
              'id' => $_POST['account_id']
            ))->puff();
        }
        
        if (!$AccountORM) {
          $this->setAjaxResponse(null, 'No existe la cuenta', true);
        } else {
          // Set account data
          $AccountORM->name     = $_POST['account_name'];
          $AccountORM->color    = $_POST['account_color'];
          $AccountORM->save();
          
          // Add initial movement to new account
          if ($_POST['account_id'] == 0) {
            $MovementORM = new MovementORM();
            $MovementORM->amount = $_POST['account_init_amount'];
            $MovementORM->setParent($AccountORM);
            $MovementORM->users_id = $this->UserData->id;
            $MovementORM->type = 1;
            $MovementORM->concept = 'Monto inicial';
            $MovementORM->save();
            unset($MovementORM); // Useless
          }
          
          // Only if is a new account render will be created, else then just returs
          // account's json object to update the DOM.
          $account_html = null;
          $account_json = null;
          
          if ($_POST['account_id'] != 0) {
            $account_json = json_encode($AccountORM);
          } else {
            $account_html = $this->renderView(
              'home/account.php',
              array('AccountORM' => $AccountORM),
              true
            );
          }
          
          // Return HTML render (or json) and message
          $this->setAjaxResponse(array(
            'account_html' => $account_html,
            'account_json' => $account_json,
            
            // Add total amounts to update DOM
            'total_amounts' => $this->getUserTotalAmounts()
            ), 'Cuenta "'.$_POST['account_name'].'" guardada.');
        }
      } catch (QuarkORMException $e) {
        $this->setAjaxResponse(null, 'No se pudo guardar la nueva cuenta', true);
      }
    }
  }
  
  /**
   * Return the total amount avaiable in the account specified by the ID $account_id
   * @param int $account_id Account ID
   * @return int
   */
  protected function getAccountAmount($account_id)
  {
    $MovementsIn = MovementORM::query()
      ->sum('amount')
      ->where(array(
        'users_id' => $this->UserData->id,
        'accounts_id' => $account_id,
        'type' => 1
      ))
      ->exec();
    
    $MovementsOut = MovementORM::query()
      ->sum('amount')
      ->where(array(
        'users_id' => $this->UserData->id,
        'accounts_id' => $account_id,
        'type' => 0
      ))
      ->exec();
      
    return $MovementsIn->amount - $MovementsOut->amount;
  }
  
  /**
   * Calculate and returns the user total amounts for
   * "total", "payments" and "available"
   * 
   * @return array
   */
  protected function getUserTotalAmounts()
  {
    $total_amounts = array(
      'total'     => 0,
      'payments'  => 0,
      'available' => 0
    );
    
    // Total is equal to all IN movements minus all OUT movements
    $total_amounts['total'] = MovementORM::query()->sum('amount')
      ->where(array(
        'users_id' => $this->UserData->id,
        'type' => 1
      ))->puff()->amount - MovementORM::query()->sum('amount')
      ->where(array(
        'users_id' => $this->UserData->id,
        'type' => 0
      ))->puff()->amount;
    
    // Payments is equal to SUM all payment's amounts
    $total_amounts['payments'] = PaymentORM::query()->sum('amount')
      ->where(array('users_id' => $this->UserData->id))
      ->puff()->amount;
      
    // Available is total minus payments OMFG!
    $total_amounts['available']
      = $total_amounts['total'] - $total_amounts['payments'];
      
    // Now format numbers to not javascript it.
    $total_amounts['total_formated']
      = '$'. number_format($total_amounts['total'], 2);
    $total_amounts['payments_formated']
      = '$'. number_format($total_amounts['payments'], 2);
    $total_amounts['available_formated']
      = '$'. number_format($total_amounts['available'], 2);
      
    return $total_amounts;
  }
  
  /**
   * Close user session, this method is called by a route, see config file.
   */
  public function logout()
  {
    $this->QuarkSess->kill();
    $this->__quarkAccessDenied();
  }
  
  public function checkAuthentication()
  {
    // If user cancel authorization back to login.
    if (!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])) {
      $this->__quarkAccessDenied();
    } else {
      // Get access token
      $TwitterAPI = $this->getTwitterAPI();
      try {
        $access_token = $TwitterAPI->getAccessToken(
          $_GET['oauth_verifier'],
          $_GET['oauth_token']
        );
        
        // Verify if this user exists in database
        $UserORM = UserORM::query()
          ->findOne()
          ->where(array('screen_name' => $access_token['screen_name']))
          ->exec();
          
        // Insert new user data if not exists
        if (!$UserORM) {
          $UserORM = new UserORM();
          $UserORM->screen_name = $access_token['screen_name'];
          $UserORM->save();
        }
        
        // Setup the session and continue to home/index
        $this->QuarkSess->set('UserData', $UserORM);
        $this->QuarkSess->setAccessLevel(1);
        header('Location:'. $this->QuarkURL->getBaseURL());
        
      } catch (TwitterAPIException $e) {
        // Fail to get access token.
        Quark::log('Fail to get access token: '. $e->getMessage());
        $this->__quarkAccessDenied();
      }
    }
  }
}
