<?php
$this
  ->appendCssFiles('home/index.css')
  ->renderView('layout/header.php');
?>

<h3>Cuentas de <?php
  echo $this->QuarkStr->esc($this->QuarkSess->get('User')->name);
?></h3>

<div class="btn-toolbar">
  <button type="button" class="btn" id="btn_add_account">
    <i class="icon-plus-sign"></i>
    Agregar cuenta
  </button>
  <button type="button" class="btn" id="btn_add_payment">
    <i class="icon-plus-sign"></i>
    Agregar pago
  </button>
</div>

<div class="row-fluid">
  <div class="span4"><h4>Capital</h4></div>
  <div class="span4"><h4>Gastos</h4></div>
  <div class="span4"><h4>Disponible</h4></div>
</div>
<div class="row-fluid">
  <div class="span4" id="amount_entire">---</div>
  <div class="span4" id="amount_payments">---</div>
  <div class="span4" id="amount_available">---</div>
</div>
<form action="javascript:;" id="frm_payments" class="clearfix">
  <?php
  /*
   * Render payments
   */
  foreach($payments as $Payment):
    $this->renderView('home/payment.php', array('Payment' => $Payment));
  endforeach;
  /* END: Payments render */ ?>
</form>
<!-- cuentas -->
<div id="accounts" class="clearfix"></div>
<?php
$this
  ->appendJsFiles('lib/dynamic-inputs.js', 'lib/accounts-gui.js', 'home/index.js')
  ->renderView('layout/footer.php');
?>
