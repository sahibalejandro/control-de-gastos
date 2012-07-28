<div id="account_<?php echo $AccountORM->id; ?>" class="account">
  <div class="account_header"
    style="background-color: #<?php echo $AccountORM->color; ?>;">
    <div class="btn-group pull-right">
      <a href="#" class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
        <li><a href="#"><i class="icon-pencil"></i> Editar...</a></li>
        <li><a href="#"><i class="icon-trash"></i> Borrar cuenta</a></li>
      </ul>
    </div>
    <h4>
      &bull; <?php echo $this->QuarkStr->esc($AccountORM->name); ?>
    </h4>
  </div>
  <div class="account_body">
    <div class="movements_list"><?php
    // Retrieve the last 10 movements from this account and render them.
    $last_movements = MovementORM::getSince($this->UserData->id, $AccountORM->id);
    if(count($last_movements) > 0):
      foreach($last_movements as $MovementORM):
        $this->renderView('home/movement.php', array('MovementORM' => $MovementORM));
      endforeach;
      // Add button to load more movements, using the last $MovementORM object
      // used in the foreach to get max_timestamp
      ?>
      <a href="#" class="btn_more_movements"
        data-account-id="<?php echo $AccountORM->id; ?>"
        data-max-timestamp="<?php echo strtotime($MovementORM->date); ?>">Mostrar más...</a>
    <?php endif; ?>
    </div>
  </div>
</div>
