<div id="payment_<?php echo $Payment->id; ?>" class="payment">
  <input type="text" class="dynamic_input concept"
    value="<?php echo $this->QuarkStr->esc($Payment->concept); ?>"
    data-input-data='<?php
      echo json_encode(array(
        'id'       => $Payment->id,
        'orm'      => 'Payment',
        'field'    => 'concept',
        // Usar &apos; en lugar de comilla simple para no romper el JSON
        'original' => str_replace("'", '&apos;', $Payment->concept),
      )); ?>'>
  <input type="text" class="dynamic_input currency"
    value="<?php echo $Payment->amount; ?>"
    data-input-data='<?php
      echo json_encode(array(
        'id'       => $Payment->id,
        'orm'      => 'Payment',
        'field'    => 'amount',
        'original' => $Payment->amount
      )); ?>'>
  <button class="btn btn-danger"
    title="Borrar pago"
    type="button"
    data-target-id="<?php echo $Payment->id; ?>"><i class="icon-remove-sign icon-white"></i></button>
</div>
