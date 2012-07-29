<!-- BEGIN Movement = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
<div id="movement_<?php echo $MovementORM->id; ?>"
  class="movement <?php echo $MovementORM->type ? 'in' : 'out'; ?>"
  data-json="<?php echo $this->QuarkStr->esc(json_encode($MovementORM)); ?>">
  <div class="clearfix">
    <div class="movement_amount">$<?php
      echo number_format($MovementORM->amount, 2);
    ?></div>
    <div class="movement_date"><?php
      echo strftime('%a %d de %b, %Y', strtotime($MovementORM->date));
    ?></div>
    <div class="movement_actions btn-group pull-right">
      <a href="#" class="btn btn-mini dropdown-toggle"
        data-toggle="dropdown"
        title="Menu"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="#"
          class="btn_movement_edit"
          data-movement-id="<?php echo $MovementORM->id; ?>"
          data-account-id="<?php echo $MovementORM->accounts_id; ?>"><i class="icon-pencil"></i> Editar...</a></li>
        <li><a href="#"
          class="btn_movement_change_type"
          data-movement-id="<?php echo $MovementORM->id; ?>"><i class="icon-refresh"></i> Deposito &lrarr; gasto</a></li>
        <li><a href="#"
          class="btn_movement_delete"
          data-movement-id="<?php echo $MovementORM->id; ?>"><i
            class="icon-trash"></i> Borrar</a></li>
      </ul>
    </div>
  </div>
  <div class="movement_concept"><?php
    echo $this->QuarkStr->esc($MovementORM->concept);
  ?></div>
</div>
<!-- END Movement = = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
