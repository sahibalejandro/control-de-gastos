<?php
$this->appendCssFiles('home/index.css')->renderView('layout/header.php');
?>
<!-- BEGIN: Accounts actiosn nav = = = = = = = = = = = = = = = = = = = = = = = = -->
<ul class="nav nav-pills">
  <li class="dropdown"><a href="#" class="dropdown-toggle"
    data-toggle="dropdown">Nuevo <span class="caret"></span></a>
    <ul class="dropdown-menu">
      <li><a href="#" id="btn_add_account">
        <i class="icon-list-alt"></i> Nueva cuenta...</a>
      </li>
      <li><a href="#" id="btn_add_payment">
        <i class="icon-flag"></i> Nuevo pago...</a>
      </li>
    </ul>
  </li>
</ul>
<!-- END: Accounts actiosn nav = = = = = = = = = = = = = = = = = = = = = = = = = -->
<!-- BEGIN: Add account modal dialog = = = = = = = = = = = = = = = = = = = = = = -->
<div class="modal hide" id="modal_add_account">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h3>Crear nueva cuenta</h3>
  </div>
  <div class="modal-body">
    <form action="javascript:;" id="frm_add_account" class="form-horizontal">
      <fieldset>
        <div class="control-group">
          <label for="account_name" class="control-label">Nombre:</label>
          <div class="controls">
            <input type="text" name="account_name" id="account_name"
              class="input-xlarge" required="required" value="">
          </div>
        </div>
        <div class="control-group">
          <label for="account_init_amount"
            class="control-label">Monto inicial: $</label>
          <div class="controls">
            <input type="text" name="account_init_amount" id="account_init_amount"
              class="input-small" required="required" value="100.00">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label">Color:</label>
          <input type="hidden" name="account_color" id="account_color" value="">
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
    </form>
  </div>
  <div class="modal-footer">
    <button type="button" id="btn_submit_add_account"
      class="btn btn-success">Agregar</button>
    <button type="button" class="btn" data-dismiss="modal">Cerrar</button>
  </div>
</div>
<!-- END: Add account modal dialog = = = = = = = = = = = = = = = = = = = = = = = -->

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
  'lib/jquery.mousewheel.js',
  'home/index.js')
  ->renderView('layout/footer.php');
?>
