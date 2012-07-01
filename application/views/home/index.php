<?php
$this
  ->appendCssFiles('home/index.css')
  ->renderView('layout/header.php');
?>

<h3>Cuentas de <?php
  echo $this->QuarkStr->esc($this->QuarkSess->get('User')->name);
?></h3>

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
<a href="#" class="btn">Agregar cuenta</a>
<!-- cuentas -->
<div id="accounts" class="clearfix"></div>
<?php
$this
  ->appendJsFiles('lib/dynamic-inputs.js', 'home/index.js')
  ->renderView('layout/footer.php');
?>
