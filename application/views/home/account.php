<?php
/**
 * Vista para mostrar una sola cuenta.
 * @author Sahib J. Leo
 */
?>
<form class="account" id="account_<?php echo $Account->id ?>">
  <div class="account_header clearfix">
    <div class="account_toolbar">
      <div class="btn-group">
        <button type="button"
          class="btn btn-success btn-small account_btn_add_movement"
          title="Agregar movimiento en <?php
            echo $this->QuarkStr->esc($Account->name); ?>"
          data-target="<?php echo $Account->id ?>">
          <i class="icon-plus-sign icon-white"></i>
        </button>
        <button class="btn btn-danger btn-small dropdown-toggle"
          data-toggle="dropdown" type="button"
          id="btn_delete_account_<?php echo $Account->id; ?>">
          <i class="icon-trash icon-white"></i>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li><a href="#" class="account_btn_trigger_delete"
            data-target="<?php echo $Account->id ?>">Borrar la cuenta</a></li>
          <li><a href="javascript:;">No borrar nada</a></li>
        </ul>
      </div>
    </div>
    <div class="account_name">&raquo; <?php
      echo $this->QuarkStr->esc($Account->name);
    ?></div>
    <!-- TODO: Implement account stats.
    <span class="account_available">1000</span>
    <span class="account_total_in">1000</span>
    <span class="account_total_out">1000</span>
    -->
  </div>
  <div class="movements">
    <div class="movements_list_header clearfix">
      <div class="movement_cell_type">Tipo</div>
      <div class="movement_cell_amount">Monto</div>
      <div class="movement_cell_date">Fecha</div>
      <div class="movement_cell_concept">Concepto</div>
      <div class="movement_cell_delete">Borrar</div>
    </div>
    <div class="movements_list">
      <?php foreach($Account->getMovements() as $Movement):
        $this->renderView('home/movement.php', array('Movement' => $Movement));
      endforeach; ?>
    </div>
  </div>
</form>
