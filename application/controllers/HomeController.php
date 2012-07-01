<?php
class HomeController extends SpendControlController
{
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultAccessLevel(1);
    $this->setActionsAccessLevel(array(
      'ajaxLogin' => 0,
      'logout' => 0
    ));
  }

  public function ajaxLoadAccounts()
  {
    try{
      $accounts = Account::query()
        ->find()
        ->where(array('user_id'=>$this->QuarkSess->get('User')->id))
        // En orden inverso, en JS las insertamos una sobre la otra.
        ->order('name', 'DESC')
        ->exec();

      // Renderizar cada vista por separado
      $renders = array();
      foreach($accounts as $Account){
        $movements = $Account->getChilds('Movement')
          ->order('date', 'DESC')
          ->order('id', 'DESC')
          ->exec();
        $renders[] = $this->renderView('home/account.php'
          , array('Account' => $Account, 'movements' => &$movements), true);
      }
      
      // Devolver el array de todos los renders
      $this->setAjaxResponse($renders);

    }catch(QuarkORMException $e){
      $this->setAjaxResponse(null, 'No se pudo cargar las cuentas', true);
    }
  }

  public function ajaxLoadTotalAmounts()
  {
    // Where para todas las consultas
    $where = array('user_id' => $this->QuarkSess->get('User')->id);

    try{
      // Ingresos en todas las cuentas
      $income = Movement::query()
        ->sum('amount')
        ->where($where)
        ->where("type=1")
        ->exec()
        ->amount;

      // Egresos en todas las cuentas
      $expenses = Movement::query()
        ->sum('amount')
        ->where($where)
        ->where("type=0")
        ->exec()
        ->amount;

      // Disponible en todas las cuentas
      $amount_entire = $income - $expenses;

      // Gastos fijos
      $amount_payments = Payment::query()
        ->sum('amount')
        ->where($where)
        ->exec()
        ->amount;

      // Disponible para gastar
      $amount_available = $amount_entire - $amount_payments;

      // El poder de JSON
      $this->setAjaxResponse(array(
        'amount_entire' => number_format($amount_entire, 2, '.', ''),
        'amount_payments' => number_format($amount_payments, 2, '.',''),
        'amount_available' => number_format($amount_available, 2, '.',''),
      ));
    }catch(QuarkORMException $e){
      $this->setAjaxResponse(null, 'No se pudo cargar los montos totales.', true);
    }
  }

  public function ajaxAddMovement()
  {
    try {
      // Primero validar que el ID de la cuenta pertenece a una cuenta del usuario
      // actual.
      $user_id = $this->QuarkSess->get('User')->id;
      $count = Account::query()
        ->count()
        ->where(array('id' => $_POST['account_id'], 'user_id' => $user_id))
        ->puff();

      if($count == 0){
        $this->setAjaxResponse(null, 'La cuenta no pertenece al usuario.', true);
      } else {
        $Movement = new Movement();
        $Movement->user_id = $user_id;
        $Movement->account_id = $_POST['account_id'];
        $Movement->amount = 0.00;
        $Movement->date = new QuarkSQLExpression('NOW()');
        $Movement->type = 1;
        $Movement->concept = 'Concepto...';
        $Movement->save();

        // Devolvemos el render del movimiento
        $this->setAjaxResponse($this->renderView('home/movement.php', array(
          'Movement' => $Movement
        ), true));
      }

    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo agregar el movimiento.', true);
    }
  }

  public function ajaxDeleteMovement()
  {
    try {
      $rows = Movement::query()->delete()
        ->where(array('id' => $_POST['id']))
        ->where(array('user_id' => $this->QuarkSess->get('User')->id))
        ->puff();
      if($rows == 0){
        $this->setAjaxResponse(null, 'No se borró ningún movimiento.', true);
      }
    } catch (QuarkORMException $e) {
      $this->setAjaxResponse(null, 'No se pudo borrar el movimiento.', true);
    }
  }

  public function ajaxChangeMovementType()
  {
    try{
      $rows = Movement::query()->update(array('type' => $_POST['new_type']))
        ->where(array('id' => $_POST['id']))
        ->where(array('user_id' => $this->QuarkSess->get('User')->id))
        ->puff();
      if($rows == 0){
        $this->setAjaxResponse(null, 'No se actualizó el tipo', true);
      }
    }catch(QuarkORMException $e){
      $this->setAjaxResponse(null, 'No se pudo cambiar el tipo de movimiento.'
        , true);
    }
  }

  public function ajaxUpdateField()
  {
    try{
      $QueryBuilder = new QuarkORMQueryBuilder($_POST['orm']);
      $rows = $QueryBuilder->update(array($_POST['field'] => $_POST['value']))
        ->where(array('id' => $_POST['id']))
        ->where(array('user_id' => $this->QuarkSess->get('User')->id))
        ->puff();

      if($rows == 0){
        $this->setAjaxResponse(null, 'Hax detectado... lamer! t(^_^t)'
          , true);
      }
    }catch(QuarkORMException $e){
      $this->setAjaxResponse(null, 'No se pudo actualizar el valor', true);
    }
  }

  public function ajaxLogin()
  {
    // Obtener los datos del usuario.
    $User = User::query()
      ->selectOne('id','name')
      ->where(array('email' => $_POST['email'], 'pass' => md5($_POST['pass'])))
      ->exec();
    
    if(!$User){
      // Usuario no encontrado
      $this->setAjaxResponse(null, 'EMail y/o contraseña incorrectos.', true);
    } else {
      // Usuario encontrado
      $this->QuarkSess->setAccessLevel(1);
      $this->QuarkSess->set('User', $User);

      // Guardar cookie si el usuario quiere ser recordado
      if(isset($_POST['remember'])){
        $this->QuarkSess->saveCookie();
      }

      $this->setAjaxResponse(true);
    }
  }

  /**
   * Cierra la sesión y muestra el login.
   * @return [type] [description]
   */
  public function logout()
  {
    $this->QuarkSess->kill();
    // Despues de matar una sesión es necesario hacer reload para
    // que la cookie sea eliminada y evitar conflictos.
    header('Location:' . $this->QuarkURL->getBaseURL() );
  }
  
  /**
   * Muestra la interfaz para editar las cuentas del usuario
   */
  public function index()
  {
    $this->renderView();
  }
}
