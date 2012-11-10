<?php
$this
  ->appendCssFiles(
    'lib/jqueryui/smoothness/jquery-ui-1.8.22.custom.css',
    'estadisticas/index.css'
  )
  ->renderView('layout/header.php');
?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<h3>Grafica general</h3>
<form id="frm_account_filter" action="javascript:;" class="form-horizontal">
<label>Filtrar por cuenta:</label>
<select name="account_id" id="account_id">
  <option value="0" selected="selected">-- Todo --</option>
  <?php foreach ($accounts as $AccountORM): ?>
  <option value="<?php echo $AccountORM->id; ?>"><?php
    echo $this->QuarkStr->esc($AccountORM->name);
  ?></option>
  <?php endforeach; ?>
</select>
<button id="btn_chart" class="btn btn-success" type="submit">Grafica</button>
</form>
<div id="chart">Cargando...</div>
<?php
// Send the account ID to JavaScript to auto-load the account chart
if ($account_id > 0):
?>
<script type="text/javascript">var ACCOUNT_ID_AUTOLOAD = <?php
  echo $account_id;
?>;</script>
<?php
endif;

$this
  ->appendJsFiles(
    'lib/jquery-ui-1.8.22.custom.min.js',
    'lib/jquery.ui.datepicker-es.js',
    'estadisticas/index.js'
  )
  ->renderView('layout/footer.php');
?>
