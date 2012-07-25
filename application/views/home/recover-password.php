<?php
$this->renderView('layout/header.php');
?>
<h2>Recuperar contraseña</h2>
<form action="javascript:;" id="frm_recover_password">
  <label for="email">Correo electrónico con que te registraste:</label>
  <input type="email" name="email" id="email" required="required">
  <div class="form-actions">
    <button type="submit" id="btn_send" class="btn btn-success">Recuperar contraseña</button>
  </div>
</form>
<?php
$this->appendJsFiles('home/recover-password.js')->renderView('layout/footer.php');
?>
