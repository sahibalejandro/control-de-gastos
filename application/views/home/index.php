<?php
$this->appendCssFiles(
  'lib/jqueryui/smoothness/jquery-ui-1.8.22.custom.css', 'home/index.css')
  ->renderView('layout/header.php');
?>
<!-- BEGIN: Accounts actiosn nav = = = = = = = = = = = = = = = = = = = = = = = = -->
<div class="btn-toolbar">
<div class="btn-group">
<a href="#" class="btn dropdown-toggle"
    data-toggle="dropdown">Nuevo <span class="caret"></span></a>
    <ul class="dropdown-menu">
      <li><a href="#" id="btn_add_account">
        <i class="icon-list-alt"></i> Nueva cuenta...</a>
      </li>
      <li><a href="#" id="btn_add_payment">
        <i class="icon-flag"></i> Nuevo pago...</a>
      </li>
    </ul>
</div>
</div>

<!-- END: Accounts actiosn nav = = = = = = = = = = = = = = = = = = = = = = = = = -->

<!-- BEGIN: User total amounts = = = = = = = = = = = = = = = = = = = = = = = = = -->
<div class="row">
  <div id="total_amount_total_label" class="span4">Total:</div>
  <div id="total_amount_payments_label" class="span4">Pagos por realizar:</div>
  <div id="total_amount_available_label" class="span4">Disponible:</div>
</div>
<div class="row">
  <div id="total_amount_total" class="span4"><?php
    echo $total_amounts['total_formated']; ?></div>
  <div id="total_amount_payments" class="span4"><?php
    echo $total_amounts['payments_formated']; ?></div>
  <div id="total_amount_available" class="span4"><?php
    echo $total_amounts['available_formated']; ?></div>
</div>
<!-- END: User total amounts = = = = = = = = = = = = = = = = = = = = = = = = = -->

<div id="payments_bar" class="clearfix">
  <!-- payments are inserted here -->
</div>

<!-- BEGIN: Edit account modal dialog = = = = = = = = = = = = = = = = = = = = = = -->
<div class="modal hide" id="modal_edit_account">
  <form action="javascript:;" id="frm_edit_account" class="form-horizontal">
    <input type="hidden" id="account_id" name="account_id" value="0">
    <input type="hidden" name="account_color" id="account_color" value="">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3>Editar cuenta<span id="edit_account_name"><!-- shows account's name --></span></h3>
    </div>
    <div class="modal-body">
        <fieldset>
          <div class="control-group">
            <label for="account_name" class="control-label">Nombre:</label>
            <div class="controls">
              <input type="text" name="account_name" id="account_name"
                class="input-xlarge" required="required" autocomplete="off" value="">
            </div>
          </div>
          <div id="edit_account_init_amount" class="control-group">
            <label for="account_init_amount"
              class="control-label">Monto inicial:</label>
            <div class="controls">
              <div class="input-prepend">
                <span class="add-on">$</span><input type="text" name="account_init_amount" id="account_init_amount"
                class="span2" required="required" autocomplete="off" value="1.00">
              </div>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label">Color:</label>
            <div id="account_color_selectors" class="controls clearfix">
              <a href="#" id="btn_color_selector_blue"
                class="btn_color_selector active" data-color="0074CC"></a>
              <a href="#" id="btn_color_selector_aqua"
                class="btn_color_selector" data-color="49AFCD"></a>
              <a href="#" id="btn_color_selector_green"
                class="btn_color_selector" data-color="5BB75B"></a>
              <a href="#" id="btn_color_selector_orange"
                class="btn_color_selector" data-color="FAA732"></a>
              <a href="#" id="btn_color_selector_red"
                class="btn_color_selector" data-color="DA4F49"></a>
            </div>
          </div>
        </fieldset>
    </div>
    <div class="modal-footer">
      <button type="submit" id="btn_save_account" class="btn btn-success">Agregar</button>
      <a href="#" class="btn" data-dismiss="modal">Cerrar</a>
    </div>
  </form>
</div>
<!-- END: Edit account modal dialog = = = = = = = = = = = = = = = = = = = = = = = -->

<!-- BEGIN: Select "account to pay" modal dialog = = = = = = = = = = = = = = = = -->
<div id="modal_pay_payment" class="modal hide">
  <form action="javascript:;" id="frm_pay_payment" class="form-vertical">
    <input type="hidden" name="payment_to_pay_id" id="payment_to_pay_id" value="0">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3>Pagar: <span id="payment_to_pay_name"></span> </h3>
    </div>
    <div class="modal-body">
      <fieldset>
        <div class="control-group">
          <label for="account" class="control-label">
            Se cargará un monto de <span id="payment_to_pay_amount"></span> en la cuenta:
          </label>
          <div class="controls">
            <select name="pay_account_id" id="pay_account_id"></select>
          </div>
        </div>
      </fieldset>
    </div>
    <div class="modal-footer">
      <button type="submit" id="btn_pay" class="btn btn-success">Pagar</button>
      <button type="button" class="btn" data-dismiss="modal">Cerrar</button>
    </div>
  </form>
</div>
<!-- END: Select "account to pay" modal dialog = = = = = = = = = = = = = = = = = -->

<!-- BEGIN: Edit movement modal dialog = = = = = = = = = = = = = = = = = = = = = -->
<div id="modal_edit_movement" class="modal hide">
  <form action="javascript:;" id="frm_movement" class="form-horizontal">
    <input type="hidden" name="movement_id" id="movement_id" value="0">
    <input type="hidden" name="movement_date" id="movement_date" value="">
    <input type="hidden" name="movement_account_id" id="movement_account_id" value="">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3>Movimiento en: <span id="movement_account_name"></span></h3>
    </div>
    <div class="modal-body">
      <fieldset>
        
        <!-- Movement concept -->
        
        <div class="control-group">
          <label for="movement_concept" class="control-label">Concepto:</label>
          <div class="controls">
            <input type="text" class="input-xlarge" name="movement_concept" id="movement_concept" required="required" autocomplete="off">
          </div>
        </div>
        
        <!-- Movement amount -->
        
        <div class="control-group">
          <label for="movement_amount" class="control-label">Monto:</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on">$</span><input type="text"
                name="movement_amount" id="movement_amount" class="span2"
                required="required" autocomplete="off">
            </div>
          </div>
        </div>
        
        <!-- Movement date -->
        
        <div class="control-group">
          <label for="movement_date_gui" class="control-label">
            Fecha:
          </label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-calendar"></i></span><input
                type="text" id="movement_date_gui" class="span2">
            </div>
          </div>
        </div>
        
        <!-- Movement type -->
        
        <div class="control-group">
          <label class="control-label">Tipo:</label>
          <div class="controls">
            <label class="radio inline">
              <input type="radio" name="movement_type" checked="checked" value="1">
              Deposito
            </label>
            <label class="radio inline">
              <input type="radio" name="movement_type" value="0">
              Gasto
            </label>
          </div>
        </div>
        
      </fieldset>
    </div>
    <div class="modal-footer">
      <button type="submit" id="btn_save_movement" class="btn btn-success">Guardar</button>
      <a href="#" class="btn" data-dismiss="modal">Cerrar</a>
    </div>
  </form>
</div>
<!-- END: Edit movement modal dialog = = = = = = = = = = = = = = = = = = = = = = -->

<!-- BEGIN: Delete account confirm dialog = = = = = = = = = = = = = = = = = = = = -->
<div id="modal_delete_account" class="modal hide">
  <div class="modal-header">
    <a href="#" class="close" data-dismiss="modal">&times;</a>
    <!-- <span> is for show the account data to delete -->
    <h3>Borrar cuenta: <span id="delete_account_name"></span></h3>
  </div>
  <div class="modal-body">
    <p>Al borrar una cuenta también se borrarán los siguientes elementos:</p>
    <p>
      <ul>
        <li>Movimientos realizados sobre la cuenta.</li>
        <li>Historial de pagos realizados con la cuenta.</li>
      </ul>
    </p>
    <p>¿Estas seguro de borrar la cuenta?<p>
  </div>
  <div class="modal-footer">
    <button type="button" id="btn_delete_account" class="btn btn-danger">Sí, borrar</button>
    <button type="button" class="btn" data-dismiss="modal">Cancelar</button>
  </div>
</div>
<!-- END: Delete account confirm dialog = = = = = = = = = = = = = = = = = = = = = -->

<!-- BEGIN: Dialog to edit payment = = = = = = = = = = = = = = = = = = = = = = = -->
<div id="modal_payment" class="modal hide">
  <form id="frm_payment" class="form-horizontal" action="javascript:;">
    <input type="hidden" name="payment_id" id="payment_id" value="0">
    <div class="modal-header">
      <a href="#" class="close" data-dismiss="modal">&times;</a>
      <h3>Editar pago</h3>
    </div>
    <div class="modal-body">
      <fieldset>
        
        <!-- Payment concept -->
        
        <div class="control-group">
          <label class="control-label" for="payment_concept">Concepto:</label>
          <div class="controls">
            <input type="text" name="payment_concept" id="payment_concept"
              required="required" value="">
          </div>
        </div>
        
        <!-- Payment amount -->
        
        <div class="control-group">
          <label class="control-label" for="payment_amount">Monto:</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on">$</span><input type="text"
                name="payment_amount" id="payment_amount"
                class="span2" required="required" value="1.00">
            </div>
          </div>
        </div>
        
      </fieldset>
    </div>
    <div class="modal-footer">
      <button id="btn_save_payment" class="btn btn-success" type="submit">Guardar</button>
      <button class="btn" type="button" data-dismiss="modal">Cerrar</button>
    </div>
  </form>
</div>
<!-- END: Dialog to edit payment = = = = = = = = = = = = = = = = = = = = = = = = -->

<!-- BEGIN: Accounts list = = = = = = = = = = = = = = = = = = = = = = = = = = =  -->
<div id="accounts_list" class="clearfix"><?php
  // Render the user accounts
  foreach($user_accounts as $AccountORM):
    $this->renderView('home/account.php', array('AccountORM' => $AccountORM));
  endforeach;
?></div>
<!-- END: Accounts list = = = = = = = = = = = = = = = = = = = = = = = = = = = =  -->
<?php
$this->appendJsFiles(
  'lib/jquery-ui-1.8.22.custom.min.js',
  'lib/jquery.ui.datepicker-es.js',
  'lib/jquery.mousewheel.js',
  'home/AccountsHandler.js',
  'home/MovementsHandler.js',
  'home/PaymentObj.js',
  'home/PaymentsHandler.js',
  'home/index.js')
  ->renderView('layout/footer.php');
?>
