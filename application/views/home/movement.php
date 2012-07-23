<?php
/**
 * Vista para mostrar un solo movimiento.
 * @author Sahib J. Leo
 */
?>
<div class="movement clearfix" id="movement_<?php echo $Movement->id ?>">
  <!-- Tipo de movimiento -->
  <div class="movement_cell_type"><button type="button" class="btn movement_btn_type <?php
      echo ($Movement->type == 1) ? 'btn-success' : 'btn-warning'; ?>"
      title="Cambiar tipo de movimiento (ingreso/egreso)"
      data-type="<?php echo $Movement->type; ?>"
      data-target="<?php echo $Movement->id; ?>"><i class="<?php
        echo ($Movement->type == 1) ? 'icon-plus-sign' : 'icon-minus-sign';
        ?>"></i></button>
  </div>

  <!-- Monto de movimiento -->
  <div class="movement_cell_amount">
    <input type="text" class="dynamic_input currency"
      value="<?php echo $Movement->amount; ?>"
      data-input-data='<?php
        echo json_encode(array(
          'id' => $Movement->id,
          'orm' => 'Movement',
          'field' => 'amount',
          'original' => $Movement->amount,
        ));
      ?>'/>
  </div>

  <!-- Fecha de movimiento -->
  <div class="movement_cell_date">
    <input type="text" class="dynamic_input date"
      value="<?php echo $Movement->date; ?>"
      data-input-data='<?php
        echo json_encode(array(
          'id' => $Movement->id,
          'orm' => 'Movement',
          'field' => 'date',
          'original' => $Movement->date,
        ));
      ?>'/>
  </div>
  
  <!-- Concepto de movimiento -->
  <div class="movement_cell_concept">
    <input type="text" class="dynamic_input concept"
      value="<?php echo $this->QuarkStr->esc($Movement->concept); ?>"
      data-input-data='<?php
        echo json_encode(array(
          'id' => $Movement->id,
          'orm' => 'Movement',
          'field' => 'concept',
          // Usar &apos; en lugar de comilla simple para no romper el JSON
          'original' => str_replace("'", '&apos;', $Movement->concept),
        ));
      ?>'/>
  </div>

  <!-- Borrar movimiento -->
  <div class="movement_cell_delete"><button type="button" href="#"
    data-target="<?php echo $Movement->id ?>"
    title="Borrar movimiento (No se puede deshacer)"
    class="btn btn-danger movement_btn_delete">
    <i class="icon-remove-sign icon-white"></i>
  </button></div>
</div>
